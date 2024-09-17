$(document).ready(function(){
    $('#signupButton').click(function() {
        var formData = new FormData($('#registerForm')[0]);
        $.ajax({
            type: 'POST',
            url: '../register.php',
            data: formData, 
            processData: false,
            contentType: false,
            success: function(response){
                $('.error').remove();
                
                try {
                    var responseData = JSON.parse(response);
                    if (responseData.errors) {
                        $.each(responseData.errors, function(field, errorMessage) {
                            $('#' + field).after('<div class="error">' + errorMessage + '</div>');
                        });
                    } else if (responseData.message) {
                        alert(responseData.message);
                        window.location.href = '../logout.php'
                    }
                } catch (error) {
                    console.error('Error parsing JSON response:', error);
                }
            },
            error: function(xhr, status, error) {
                console.error('Ajax request error:', error);
            }
        });
    });
});

function calculateAge() {
        var dobInput = document.getElementById('birthday').value;
        var dob = new Date(dobInput);
        var currentDate = new Date();
        if (dob > currentDate) {
            alert("Please enter a valid date of birth (not in the future).");
            document.getElementById('birthday').value = "";               
        }else{

        var ageDiffMs = currentDate - dob;
        var ageDate = new Date(ageDiffMs);
        var age = Math.abs(ageDate.getUTCFullYear() - 1970);
        document.getElementById('age').value = age;
    }
    }
    function setupFileInputPreview(inputId, previewId) {
        const fileInput = document.getElementById(inputId);
        const previewImage = document.getElementById(previewId);

        fileInput.addEventListener('change', function(event) {
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(event) {
                    previewImage.src = event.target.result;
                    previewImage.style.display = 'block';
                };

                reader.readAsDataURL(file);
            } else {
                previewImage.src = '#';
                previewImage.style.display = 'none';
            }
        });
    }

    setupFileInputPreview('proofOfBilling', 'previewProofOfBilling');
    setupFileInputPreview('validId', 'previewValidId');
    setupFileInputPreview('coe', 'previewCoe');