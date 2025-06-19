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

let profileData;
fetch('../database/getProfile.php', {
    credentials: 'include'
    })
    .then(r => r.json())
    .then(profile => {
        profileData = profile;
        loadProfileToDisplay(profile);
});

function deleteAccount(){
    fetch('../database/deleteAccount.php')
    .then(r => r.json())
    .then(data => {
        const messageDiv = document.getElementById('messageProfileDelete');
        messageDiv.textContent = data.message;
        if(data.success){
            messageDiv.style.color = "green";
            let countdown = 3;
            messageDiv.textContent = data.message + " Redirecting to index page in: " + countdown;
            const intervalId = setInterval(() => {
                countdown--;
                if (countdown > 0) {
                    messageDiv.textContent = data.message + " Redirecting to index page in: " + countdown;
                } else {
                    clearInterval(intervalId);
                    window.location.href = "index.php";
                }
            }, 1000);
        } else {
            messageDiv.style.color = "red";
        }
    })
    .catch(err => {
        document.getElementById('messageProfileDelete').textContent = 'Error trying to delete account';
        document.getElementById('messageProfileDelete').style.color = "red";
    });
}

function createFriendItem(friend){
    const li = document.createElement('li');
    li.textContent = friend;
    return li;
}
function createFriendRequestItem(friendRequest){
    const li = document.createElement('li');
    li.textContent = friendRequest;
    return li;
}
function renderList(container, items, createItem, emptyMessage) {
    container.innerHTML = '';
    if (!items || items.length === 0) {
        container.innerHTML = `<li>${emptyMessage}</li>`;
        return;
    }
    const fragment = document.createDocumentFragment();
    items.forEach(item => fragment.appendChild(createItem(item)));
    container.appendChild(fragment);
}
function loadFriendLists() {
    fetch('../database/getFriends.php')
        .then(res => res.json())
        .then(res => {
            const friendList = document.getElementById('friendList');
            const friendRequests = document.getElementById('friendRequests');

            renderList(friendList, res.friends, createFriendItem, 'No friends found.');
            renderList(friendRequests, res.friend_requests, createFriendRequestItem, 'No friend requests found.');
        })
        .catch(err => {
            document.getElementById('friendList').innerHTML = `<li>Error loading friends.</li>`;
            document.getElementById('friendRequests').innerHTML = '<li>Error loading friend requests.</li>';
        });
}

function createQueryItem(query) {
    const li = document.createElement('li');

    const div1 = document.createElement('div');
    div1.className = "queryInfo";
    const id = document.createElement('span');
    id.innerText = query['id'];
    const name = document.createElement('span');
    name.innerText = query['name'];
    div1.appendChild(id);
    div1.appendChild(name);

    const div2 = document.createElement('div');
    div2.className = "queryButtons";
    const button1 = document.createElement('button');
    button1.innerText = "Use"; //aici iti da redirect la main page cu query-ul loaded
    const button2 = document.createElement('button');
    button2.innerText = "Rename"; //rename la query
    button2.addEventListener('click', () => { addFriendFunction(name); });
    const button3 = document.createElement('button');
    button3.innerText = "Delete"; //sterge query-ul
    div2.appendChild(button1);
    div2.appendChild(button2);
    div2.appendChild(button3);

    li.appendChild(div1);
    li.appendChild(div2);

    return li;
}
function loadQueries(){
    fetch('../database/getQueries.php')
        .then(res => res.json())
        .then(queries => {
            const queryList = document.getElementById('queriesList');
            renderList(queryList, queries, createQueryItem, 'No queries found.');
        })
        .catch(err => {
            document.getElementById('queriesList').innerHTML = '<li>Error loading queries.</li>'
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




document.addEventListener('DOMContentLoaded', function(){
    document.getElementById('editProfileBtn').addEventListener('click', function() {
        document.getElementById('profileDisplay').style.display = 'none';
        document.getElementById('profileForm').style.display = 'flex';
        document.getElementById('profileDisplayButtons').style.display = 'none';
        document.getElementById('formEditProfileButtons').style.display = 'block';
        loadProfileToForm(profileData);
    });

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


    document.getElementById('profileForm').addEventListener('submit', function (e) {
        const messageDiv = document.getElementById('messageProfileSave');
        const errorDiv = document.getElementById('profileSaveError');
        e.preventDefault();

        const formData = new FormData(document.getElementById('profileForm'));
        fetch('../database/saveProfile.php', {
            method: 'POST',
            body: formData
        })
        .then(data => {
            if (!data.success) {
                throw new Error("Error: " + data.status);
            }
            return data.json();
        })
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
            errorDiv.textContent = "Failed to save profile, please try again: " + error;
            errorDiv.style.color = "red";
        });
    });
});