@extends('layouts.dashboard')

@section('title', 'Bulk Certificate Generator')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 bg-white">
                <div class="card-body p-4">
                    <h2 class="fw-bold mb-3" style="color: #2c3e50;">Automated Bulk Certificate Generator</h2>
                    <p class="text-muted">Upload a certificate template and an Excel/CSV file with names. Drag and drop the text placeholder to set the position for the names, style it, and generate certificates in bulk.</p>
                </div>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Configuration Column -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-cogs me-2"></i> Configuration</h5>
                </div>
                <div class="card-body">
                    <form id="generatorForm" action="{{ route('gd.certificate.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">1. Certificate Template (Image)</label>
                            <input type="file" class="form-control" name="template" id="templateInput" accept="image/jpeg, image/png, image/jpg" required>
                            <small class="text-muted">Upload a clear JPG or PNG template.</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">2. Names List (Excel/CSV)</label>
                            <input type="file" class="form-control" name="names_file" accept=".xlsx, .xls, .csv" required>
                            <small class="text-muted">Upload a file with names in the <strong>first column</strong> and emails in the <strong>second column</strong>.</small>
                        </div>

                        <!-- Hidden fields to hold extracted Fabric.js data -->
                        <input type="hidden" name="x_pos" id="hiddenXPos">
                        <input type="hidden" name="y_pos" id="hiddenYPos">
                        <input type="hidden" name="font_size" id="hiddenFontSize">
                        <input type="hidden" name="font_family" id="hiddenFontFamily">
                        <input type="hidden" name="font_color" id="hiddenFontColor">
                        <input type="hidden" name="font_weight" id="hiddenFontWeight">
                        <input type="hidden" name="font_style" id="hiddenFontStyle">

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg" id="generateBtn" disabled>
                                <i class="fas fa-magic me-2"></i> Generate Certificates
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview & Editor Column -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-paint-brush me-2"></i> Canvas Editor</h5>
                </div>
                
                <!-- Tailwind Toolbar -->
                <div class="bg-light border-bottom p-3 d-flex flex-wrap gap-3 align-items-center justify-content-center" id="textToolbar" style="display: none !important;">
                    
                    <div class="d-flex align-items-center gap-2">
                        <label class="small text-muted mb-0 fw-bold">Font:</label>
                        <select class="form-select form-select-sm w-auto" id="fontFamilySelector">
                            <option value="Agency FB">Agency FB</option>
                            <option value="Algerian">Algerian</option>
                            <option value="Arial">Arial</option>
                            <option value="Arial Black">Arial Black</option>
                            <option value="Book Antiqua">Book Antiqua</option>
                            <option value="Bookman Old Style">Bookman Old Style</option>
                            <option value="Bradley Hand ITC">Bradley Hand ITC</option>
                            <option value="Britannic Bold">Britannic Bold</option>
                            <option value="Broadway">Broadway</option>
                            <option value="Calibri">Calibri</option>
                            <option value="Castellar">Castellar</option>
                            <option value="Century">Century</option>
                            <option value="Century Gothic">Century Gothic</option>
                            <option value="Chiller">Chiller</option>
                            <option value="Colonna MT">Colonna MT</option>
                            <option value="Comic Sans MS">Comic Sans MS</option>
                            <option value="Consolas">Consolas</option>
                            <option value="Constantia">Constantia</option>
                            <option value="Cooper Black">Cooper Black</option>
                            <option value="Copperplate Gothic Bold">Copperplate Gothic Bold</option>
                            <option value="Corbel">Corbel</option>
                            <option value="Courier New">Courier New</option>
                            <option value="Curlz MT">Curlz MT</option>
                            <option value="Elephant">Elephant</option>
                            <option value="Franklin Gothic Medium">Franklin Gothic Medium</option>
                            <option value="Gabriola">Gabriola</option>
                            <option value="Gadugi">Gadugi</option>
                            <option value="Garamond">Garamond</option>
                            <option value="Georgia">Georgia</option>
                            <option value="Impact">Impact</option>
                            <option value="Ink Free">Ink Free</option>
                            <option value="Leelawadee UI">Leelawadee UI</option>
                            <option value="Lucida Console">Lucida Console</option>
                            <option value="Malgun Gothic">Malgun Gothic</option>
                            <option value="Microsoft Sans Serif">Microsoft Sans Serif</option>
                            <option value="Myanmar Text">Myanmar Text</option>
                            <option value="Palatino Linotype">Palatino Linotype</option>
                            <option value="Segoe Print">Segoe Print</option>
                            <option value="Segoe Script">Segoe Script</option>
                            <option value="Segoe UI">Segoe UI</option>
                            <option value="Segoe UI Black">Segoe UI Black</option>
                            <option value="Sylfaen">Sylfaen</option>
                            <option value="Symbol">Symbol</option>
                            <option value="Tahoma">Tahoma</option>
                            <option value="Times New Roman">Times New Roman</option>
                            <option value="Trebuchet MS">Trebuchet MS</option>
                            <option value="Verdana">Verdana</option>
                            <option value="Webdings">Webdings</option>
                            <option value="Wingdings">Wingdings</option>
                        </select>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <label class="small text-muted mb-0 fw-bold">Size:</label>
                        <input type="number" class="form-control form-control-sm" id="fontSizeSelector" value="48" style="width: 70px;">
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <label class="small text-muted mb-0 fw-bold">Color:</label>
                        <input type="color" class="form-control form-control-color p-0 border-0" id="fontColorSelector" value="#000000" style="width: 30px; height: 30px;">
                    </div>

                    <div class="d-flex align-items-center gap-2 ms-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnBold" title="Bold"><i class="fas fa-bold"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnItalic" title="Italic"><i class="fas fa-italic"></i></button>
                    </div>
                </div>

                <!-- Canvas Area -->
                <div class="card-body p-0 d-flex align-items-center justify-content-center bg-secondary" style="min-height: 400px; overflow: hidden; position: relative;" id="canvasWrapper">
                    
                    <div id="noImageMessage" class="text-white p-5 text-center w-100">
                        <i class="fas fa-image fa-4x mb-3 text-light opacity-50"></i>
                        <h5>No template uploaded yet</h5>
                        <p class="text-light opacity-75">Upload a certificate template to start editing.</p>
                    </div>

                    <canvas id="certificateCanvas"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Load Fabric.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const templateInput = document.getElementById('templateInput');
        const generateBtn = document.getElementById('generateBtn');
        const form = document.getElementById('generatorForm');
        
        const textToolbar = document.getElementById('textToolbar');
        const noImageMessage = document.getElementById('noImageMessage');
        const canvasWrapper = document.getElementById('canvasWrapper');
        
        // Toolbar controls
        const fontFamilySelector = document.getElementById('fontFamilySelector');
        const fontSizeSelector = document.getElementById('fontSizeSelector');
        const fontColorSelector = document.getElementById('fontColorSelector');
        const btnBold = document.getElementById('btnBold');
        const btnItalic = document.getElementById('btnItalic');
        
        let canvas = null;
        let nameTextElement = null;
        let scaleFactor = 1; // To track scaling between original image and displayed canvas

        // Initialize Fabric canvas
        canvas = new fabric.Canvas('certificateCanvas', {
            preserveObjectStacking: true,
            selection: false
        });

        templateInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const imgObj = new Image();
                    imgObj.src = event.target.result;
                    imgObj.onload = function() {
                        setupCanvas(imgObj);
                    }
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        function setupCanvas(img) {
            // Hide placeholder, show toolbar
            noImageMessage.style.display = 'none';
            textToolbar.style.setProperty('display', 'flex', 'important');
            
            // Calculate scale to fit within wrapper while maintaining aspect ratio
            const maxWidth = canvasWrapper.clientWidth;
            const maxHeight = 600; // max visible height
            
            let displayWidth = img.width;
            let displayHeight = img.height;
            scaleFactor = 1;

            if (img.width > maxWidth) {
                scaleFactor = maxWidth / img.width;
                displayWidth = maxWidth;
                displayHeight = img.height * scaleFactor;
            }

            // Setup canvas dimensions
            canvas.setWidth(displayWidth);
            canvas.setHeight(displayHeight);

            // Set Background Image
            fabric.Image.fromURL(img.src, function(fabricImg) {
                fabricImg.set({
                    scaleX: scaleFactor,
                    scaleY: scaleFactor,
                    originX: 'left',
                    originY: 'top'
                });
                canvas.setBackgroundImage(fabricImg, canvas.renderAll.bind(canvas));
                
                // Add the draggable text element
                addDraggableText(displayWidth, displayHeight);
                generateBtn.removeAttribute('disabled');
            });
        }

        function addDraggableText(width, height) {
            // Remove old text if exists
            if (nameTextElement) {
                canvas.remove(nameTextElement);
            }

            // Create new text
            nameTextElement = new fabric.Text('[Participant Name]', {
                left: width / 2,
                top: height / 2,
                fontFamily: fontFamilySelector.value,
                fontSize: fontSizeSelector.value * scaleFactor, // Scale font visually
                fill: fontColorSelector.value,
                fontWeight: 'normal',
                fontStyle: 'normal',
                originX: 'center',
                originY: 'center',
                selectable: true,
                hasControls: false, // Hide resize borders
                hasBorders: true,
                borderColor: '#0d6efd',
                cursorColor: '#0d6efd',
                padding: 10
            });

            canvas.add(nameTextElement);
            canvas.setActiveObject(nameTextElement);
            canvas.renderAll();
        }

        // --- Toolbar Event Listeners --- //
        
        fontFamilySelector.addEventListener('change', function() {
            if (!nameTextElement) return;
            nameTextElement.set('fontFamily', this.value);
            canvas.renderAll();
        });

        fontSizeSelector.addEventListener('input', function() {
            if (!nameTextElement) return;
            // The size displayed is relative to the scale
            nameTextElement.set('fontSize', this.value * scaleFactor);
            canvas.renderAll();
        });

        fontColorSelector.addEventListener('input', function() {
            if (!nameTextElement) return;
            nameTextElement.set('fill', this.value);
            canvas.renderAll();
        });

        btnBold.addEventListener('click', function() {
            if (!nameTextElement) return;
            const currentWeight = nameTextElement.get('fontWeight');
            const newWeight = currentWeight === 'bold' ? 'normal' : 'bold';
            nameTextElement.set('fontWeight', newWeight);
            this.classList.toggle('btn-primary');
            this.classList.toggle('btn-outline-secondary');
            canvas.renderAll();
        });

        btnItalic.addEventListener('click', function() {
            if (!nameTextElement) return;
            const currentStyle = nameTextElement.get('fontStyle');
            const newStyle = currentStyle === 'italic' ? 'normal' : 'italic';
            nameTextElement.set('fontStyle', newStyle);
            this.classList.toggle('btn-primary');
            this.classList.toggle('btn-outline-secondary');
            canvas.renderAll();
        });

        // --- Form Submission Logic --- //
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!nameTextElement) {
                alert('Please upload a template and position the text.');
                return;
            }

            // Extract Data from Fabric Text Element
            // We must reverse the scale to get actual image coordinates
            const actualX = nameTextElement.left / scaleFactor;
            const actualY = nameTextElement.top / scaleFactor;
            const actualFontSize = nameTextElement.fontSize / scaleFactor;
            
            // Populate Hidden Inputs
            document.getElementById('hiddenXPos').value = Math.round(actualX);
            document.getElementById('hiddenYPos').value = Math.round(actualY);
            document.getElementById('hiddenFontSize').value = Math.round(actualFontSize);
            document.getElementById('hiddenFontFamily').value = nameTextElement.fontFamily;
            document.getElementById('hiddenFontColor').value = nameTextElement.fill;
            document.getElementById('hiddenFontWeight').value = nameTextElement.fontWeight;
            document.getElementById('hiddenFontStyle').value = nameTextElement.fontStyle;

            // Visual loading state
            generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';
            generateBtn.setAttribute('disabled', 'true');

            // Proceed with form submission
            this.submit();

            // Re-enable after delay (assuming file download kicks in)
            setTimeout(() => { 
                generateBtn.innerHTML = '<i class="fas fa-magic me-2"></i> Generate Certificates';
                generateBtn.removeAttribute('disabled');
            }, 6000);
        });
    });
</script>
@endpush
@endsection
