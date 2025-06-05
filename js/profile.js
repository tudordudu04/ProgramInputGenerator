function showPanel(panel) {
    const panels = ['profile', 'friends', 'queries', 'results'];
    panels.forEach(function(name) {
        document.getElementById(name + '-panel').style.display = (name === panel) ? 'flex' : 'none';
    });
}
function loadProfileToDisplay(profile) {
    document.getElementById('displayUsername').textContent = profile.username;
    document.getElementById('displayEmail').textContent = profile.email;
    document.getElementById('displayFirstName').textContent = profile.firstName;
    document.getElementById('displayLastName').textContent = profile.lastName;
    document.getElementById('displayPhoneNumber').textContent = profile.phoneNumber;
    document.getElementById('displayAddress').textContent = profile.address;
    document.getElementById('displayCountry').textContent = profile.country;
    document.getElementById('displayCity').textContent = profile.city;
    document.getElementById('profileName').textContent = profile.firstName + " " + profile.lastName;
    document.getElementById('profileEmail').textContent = profile.email;
    // document.getElementById('profileImage').src = ...;
}

function loadProfileToForm(profile) {
    document.getElementById('username').value = profile.username;
    document.getElementById('email').value = profile.email;
    document.getElementById('firstName').value = profile.firstName;
    document.getElementById('lastName').value = profile.lastName;
    document.getElementById('phoneNumber').value = profile.phoneNumber;
    document.getElementById('address').value = profile.address;
    document.getElementById('country').value = profile.country;
    document.getElementById('city').value = profile.city;
}

document.addEventListener('DOMContentLoaded', function(){
    let profileData;
    fetch('../database/getProfile.php', {
        credentials: 'include'
        })
        .then(r => r.json())
        .then(profile => {
            profileData = profile;
            loadProfileToDisplay(profile);
            document.getElementById('profileForm').style.display = 'none';
            document.getElementById('formEditProfileButtons').style.display = 'none';
        });

    document.getElementById('editProfileBtn').addEventListener('click', function() {
        document.getElementById('profileDisplay').style.display = 'none';
        document.getElementById('profileForm').style.display = '';
        document.getElementById('profileDisplayButtons').style.display = 'none';
        document.getElementById('formEditProfileButtons').style.display = '';
        loadProfileToForm(profileData);
    });

    // Cancel edit
    document.getElementById('cancelProfileBtn').addEventListener('click', function() {
        document.getElementById('profileDisplay').style.display = '';
        document.getElementById('profileForm').style.display = 'none';
        document.getElementById('profileDisplayButtons').style.display = '';
        document.getElementById('formEditProfileButtons').style.display = 'none';
        loadProfileToDisplay(profileData);
    });


    const form = document.getElementById('profileForm');
    const messageDiv = document.getElementById('messageProfileSave');
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(form);

        fetch('../database/saveProfile.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            messageDiv.textContent = data.message;
            messageDiv.style.color = data.success ? "green" : "red";

            profileData.username = document.getElementById('username').value;
            profileData.email = document.getElementById('email').value;
            profileData.firstName = document.getElementById('firstName').value;
            profileData.lastName = document.getElementById('lastName').value;
            profileData.phoneNumber = document.getElementById('phoneNumber').value;
            profileData.address = document.getElementById('address').value;
            profileData.country = document.getElementById('country').value;
            profileData.city = document.getElementById('city').value;
            loadProfileToDisplay(profileData);
            document.getElementById('profileDisplay').style.display = '';
            document.getElementById('profileForm').style.display = 'none';
            document.getElementById('profileDisplayButtons').style.display = '';
            document.getElementById('formEditProfileButtons').style.display = 'none';
        })
        .catch(() => {
            messageDiv.textContent = "Failed to save profile, please try again.";
            messageDiv.style.color = "red";
        });
    });
});