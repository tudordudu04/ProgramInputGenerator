function showPanel(panel) {
    const panels = ['profile', 'friends', 'queries', 'results', 'button'];
    panels.forEach(function(name) {
        document.getElementById(name + '-panel').style.display = (name === panel) ? 'flex' : 'none';
    });
    if(panel === 'friends'){
        loadFriendLists();
    } else if(panel === 'queries'){
        loadQueries();
    }
}

// function deleteAccount(){

// }

function loadFriendLists() {
    fetch('../database/getFriends.php')
        .then(res => res.json())
        .then(res => {
            const ul1 = document.getElementById('friendList');
            ul1.innerHTML = '';
            const ul2 = document.getElementById('friendRequests');
            ul2.innerHTML = '';
            if(res.friends.length === 0){
                ul1.innerHTML = '<li>No friends found.</li>';
                if(res.friend_requests.length === 0){
                    ul2.innerHTML = '<li>No friend requests found.</li>';
                    return;
                }
            }
            res.friends.forEach(friend => {
                const li = document.createElement('li');
                li.textContent = friend;
                ul1.appendChild(li);
            });
            if(res.friend_requests.length === 0){
                    ul2.innerHTML = '<li>No friend requests found.</li>';
                    return;
            }
            res.friend_requests.forEach(friend_request => {
                const li = document.createElement('li');
                li.textContent = friend_request;
                ul2.appendChild(li);
            });
        })
        .catch(err => {
            document.getElementById('friendList').innerHTML = '<li>Error loading friends.</li>';
            document.getElementById('friendRequests').innerHTML = '<li>Error loading friend requests.</li>';
        });
}
//update this

function loadQueries(){
    fetch('../database/getQueries.php')
        .then(res => res.json())
        .then(queries => {
            const ul = document.getElementById('queriesList');
            ul.innerHTML = '';
            if (queries.length === 0) {
                ul.innerHTML = '<li>No queries found.</li>';
                return;
            }
            //Top 10 cele mai proaste coduri de le-am scris
            //Nici nu stiu cum ar trebui sa fac aici sa rezolv ;((
            queries.forEach(query => {
                const li = document.createElement('li');
                const button1 = document.createElement('button');
                const button2 = document.createElement('button');
                const id = document.createElement('span');
                const name = document.createElement('span');
                id.innerText = query['id'];
                name.innerText = query['name'];
                button1.innerText = "Use" //Nu merg butoanele deocamdata
                button2.innerText = "Rename" 
                button2.addEventListener('click', () => { addFriendFunction(name); });
                const div1 = document.createElement('div');
                div1.className = "queryInfo"
                div1.appendChild(id);
                div1.appendChild(name);
                const div2 = document.createElement('div');
                div2.className = "queryButtons"
                div2.appendChild(button1);
                div2.appendChild(button2);
                li.appendChild(div1);
                li.appendChild(div2);
                ul.appendChild(li);
            });
        })
        .catch(err => {
            document.getElementById('queriesList').innerHTML = '<li>Error loading queries.</li>';
        });
}

function loadProfileToDisplay(profile) {
    document.getElementById('displayUsername').textContent = profile.username;
    document.getElementById('displayEmail').textContent = profile.email;
    document.getElementById('displayHidden').textContent = profile.hidden ? "Yes " : "No";
    document.getElementById('displayFirstName').textContent = profile.firstName;
    document.getElementById('displayLastName').textContent = profile.lastName;
    document.getElementById('displayPhoneNumber').textContent = profile.phoneNumber;
    document.getElementById('displayAddress').textContent = profile.address;
    document.getElementById('displayCountry').textContent = profile.country;
    document.getElementById('displayCity').textContent = profile.city;
    document.getElementById('profileName').textContent = profile.firstName + " " + profile.lastName;
    document.getElementById('profileEmail').textContent = profile.email;
    document.getElementById('profilePhoto').src = profile.profilePhotoUrl;
}

function loadProfileToForm(profile) {
    document.getElementById('hidden').checked = profile.hidden === true;
    document.getElementById('username').value = profile.username;
    document.getElementById('email').value = profile.email;
    document.getElementById('firstName').value = profile.firstName;
    document.getElementById('lastName').value = profile.lastName;
    document.getElementById('phoneNumber').value = profile.phoneNumber;
    document.getElementById('address').value = profile.address;
    document.getElementById('country').value = profile.country;
    document.getElementById('city').value = profile.city;
    document.getElementById('profilePhoto').src = profile.profilePhotoUrl;
}


let profileData;
fetch('../database/getProfile.php', {
    credentials: 'include'
    })
    .then(r => r.json())
    .then(profile => {
        profileData = profile;
        loadProfileToDisplay(profile);
});


document.addEventListener('DOMContentLoaded', function(){
    document.getElementById('editProfileBtn').addEventListener('click', function() {
        document.getElementById('profileDisplay').style.display = 'none';
        document.getElementById('profileForm').style.display = 'flex';
        document.getElementById('profileDisplayButtons').style.display = 'none';
        document.getElementById('formEditProfileButtons').style.display = 'block';
        loadProfileToForm(profileData);
    });

    // document.getElementById('deleteAccountBtn').addEventListener('click', deleteAccount());

    document.getElementById('fileInput').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const formData = new FormData();
        formData.append('profilePhoto', file);

        fetch('../database/saveProfilePhoto.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
        if(data.success){
            document.getElementById('profilePhoto').src = data.url;
            profileData.profilePhotoUrl = data.url;
        } else {
            alert('Upload failed: ' + data.message);
        }
        });
    }
    });

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
            profileData.hidden = document.getElementById('hidden').checked;
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
        .catch((error) => {
            messageDiv.textContent = "Failed to save profile, please try again: " + error;
            messageDiv.style.color = "red";
        });
    });
});