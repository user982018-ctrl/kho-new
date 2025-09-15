function upload() {

  const fileUploadInput = document.querySelector('.file-uploader');

  /// Validations ///

  if (!fileUploadInput.value) {
    return;
  }

  // using index [0] to take the first file from the array
  const image = fileUploadInput.files[0];

  // check if the file selected is not an image file
  if (!image.type.includes('image')) {
    return alert('Only images are allowed!');
  }

  // check if size (in bytes) exceeds 10 MB
  if (image.size > 100_000_000) {
    return alert('Maximum upload size is 100MB!');
  }

    // Lấy phần mở rộng file (jpg, jpeg, png, gif)
  const allowedExtensions = ['jpg', 'jpeg', 'png', 'gif','JPG', 'JPEG', 'PNG', 'GIF'];
  const fileExtension = image.name.split('.').pop().toLowerCase();
  if (!allowedExtensions.includes(fileExtension)) {
      alert('Chỉ chấp nhận các định dạng: JPG, JPEG, PNG, GIF!');
      return;
  }
  /// Display the image on the screen ///

  const fileReader = new FileReader();
  fileReader.readAsDataURL(image);

  fileReader.onload = (fileReaderEvent) => {
    const profilePicture = document.querySelector('.profile-picture');
    profilePicture.style.backgroundImage = `url(${fileReaderEvent.target.result})`;
  }

  // upload image to the server or the cloud
}