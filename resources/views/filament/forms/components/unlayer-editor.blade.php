<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        wire:ignore
        x-data="unlayerEditorComponent"
        x-init="init('{{ $getId() }}', '{{ $getStatePath() }}', '{{ $getMinHeight() }}', @js($getProjectId()), @js($getOptions()))"
        class="w-full"
        style="min-height: {{ $getMinHeight() }};"
    >
        <div
            id="{{ $getId() }}"
            style="height: {{ $getMinHeight() }}; width: 100%;"
            class="unlayer-editor-container"
        ></div>

        <div x-show="loading" class="text-center py-4 text-gray-500">
            <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm mt-2">Loading editor...</span>
        </div>

        <div x-show="error" x-text="errorMsg" class="text-red-500 p-4"></div>
    </div>
</x-dynamic-component>

{{-- Load Unlayer Script --}}
@once
    @push('scripts')
        <script src="https://editor.unlayer.com/embed.js"></script>
    @endpush
@endonce

{{-- Alpine Component --}}
@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('unlayerEditorComponent', () => ({
            loading: true,
            error: false,
            errorMsg: '',
            editor: null,
            statePath: null,
            editorId: null,
            height: '600px',
            saveTimeout: null,

            init(id, statePath, height, projectId, options) {
                this.editorId = id;
                this.statePath = statePath;
                this.height = height;

                console.log('🚀 Initializing Unlayer');
                console.log('Editor ID:', id);
                console.log('State Path:', statePath);
                console.log('Current State:', this.getCurrentState());

                this.waitForUnlayer(() => {
                    this.initEditor(projectId, options);
                });
            },

            getCurrentState() {
                // Get current state from Livewire
                return @this.get(this.statePath);
            },

            waitForUnlayer(callback) {
                if (typeof unlayer !== 'undefined') {
                    callback();
                    return;
                }

                let attempts = 0;
                const interval = setInterval(() => {
                    attempts++;
                    if (typeof unlayer !== 'undefined') {
                        clearInterval(interval);
                        callback();
                    } else if (attempts > 50) {
                        clearInterval(interval);
                        console.error('❌ Unlayer failed to load');
                        this.loading = false;
                        this.error = true;
                        this.errorMsg = 'Failed to load Unlayer library';
                    }
                }, 100);
            },

            initEditor(projectId, options) {
                const config = {
                    id: this.editorId,
                    displayMode: 'email',
                    projectId: projectId || null,
                    appearance: {
                        theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                    },
                    features: {
                        stockImages: !!projectId,
                        userUploads: true,
                    },
                    ...options
                };

                console.log('⚙️ Unlayer config:', config);

                try {
                    unlayer.init(config);
                    this.editor = unlayer;

                    unlayer.addEventListener('editor:ready', () => {
                        console.log('✅ Editor ready!');
                        this.loading = false;
                        this.loadExistingDesign();
                    });

                    unlayer.addEventListener('design:updated', () => {
                        console.log('📝 Design updated - scheduling save');
                        this.scheduleSave();
                    });

                } catch (error) {
                    console.error('❌ Error initializing Unlayer:', error);
                    this.loading = false;
                    this.error = true;
                    this.errorMsg = 'Error: ' + error.message;
                }
            },

            loadExistingDesign() {
                const state = this.getCurrentState();
                
                console.log('📂 Loading existing design:', state);

                if (!state) {
                    console.log('ℹ️ No existing design to load');
                    return;
                }

                try {
                    let design = state;
                    
                    // If it's a string, parse it
                    if (typeof design === 'string') {
                        design = JSON.parse(design);
                    }

                    // Check if we have a design object
                    if (design && design.design) {
                        unlayer.loadDesign(design.design);
                        console.log('✅ Loaded existing design');
                    } else {
                        console.log('⚠️ No design object found in state');
                    }
                } catch (error) {
                    console.error('❌ Error loading design:', error);
                }
            },

            scheduleSave() {
                // Debounce saves to avoid too many calls
                clearTimeout(this.saveTimeout);
                this.saveTimeout = setTimeout(() => {
                    this.saveDesign();
                }, 1000);
            },

            saveDesign() {
                if (!unlayer) {
                    console.error('❌ Unlayer not initialized');
                    return;
                }

                console.log('💾 Saving design...');

                unlayer.exportHtml((data) => {
                    const designData = {
                        design: data.design,
                        html: data.html
                    };

                    console.log('📦 Design data to save:', designData);
                    console.log('📍 Saving to path:', this.statePath);

                    // Set the data to Livewire
                    @this.set(this.statePath, designData);

                    console.log('✅ Design saved to Livewire!');
                }, {
                    mergeTags: true
                });
            },

            // Method to manually trigger save (for debugging)
            manualSave() {
                console.log('🔧 Manual save triggered');
                this.saveDesign();
            }
        }));
    });
</script>
@endpush