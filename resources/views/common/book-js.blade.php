// image preview
const validImageTypes = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];
const image = $('#image');
const imagePreview = $('#image-preview');

function handleFileInput(fileInput, preview) {
    fileInput.change(function (e) {
        let file = this.files[0];

        if (file && validImageTypes.includes(file['type'])) {
            let reader = new FileReader();
            reader.onload = function (event) {
                preview.removeClass('d-none');
                preview.attr('src', event.target.result);
            };
            reader.readAsDataURL(file);
        } else {
            fileInput.val('');
        }
    });
}

handleFileInput(image, imagePreview);