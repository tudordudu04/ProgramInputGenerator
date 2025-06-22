let profileData;
const url = new URL(window.location.href);
const friendId = url.searchParams.get('id');

fetch('database/getProfile.php?id=' + friendId, {
    credentials: 'include'
    })
    .then(r => {
        if(!r.ok)
            window.location.href = "index.php";
        return r.json();
    })
    .then(profile => {
        profileData = profile;
        loadProfileToDisplay(profile);
    })
    .catch(err => {
        alert(err);
});

function reportProfile(){
    const formData = new FormData();
    formData.append('ticketTitle', 'Report for ' + profileData.username);
    formData.append('ticketReason', 'report');
    formData.append('ticketBody', 'User:' + profileData.username + " was reported on: " + Math.floor(Date.now() / 1000));
    const message = document.getElementById('messageProfileReport');
    message.textContent = '';

    fetch('database/submitTicket.php',{
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if(data.success){
            message.style.color = 'green';
            message.textContent = 'Report submitted succesfully.';
        }
        else {
            message.style.color = 'red';
            message.textContent = 'Report couldn\'t be submitted.'; 
        }
    })
    .catch(err=>{
        message.style.color = 'red';
        message.textContent = "Error: " + err;
    })
}

function showPanel(panel) {
    const panels = ['friendProfile', 'friendFriends', 'friendQueries'];
    panels.forEach(function(name) {
        document.getElementById(name + '-panel').style.display = (name === panel) ? 'flex' : 'none';
    });
    if(panel === 'friendFriends'){
        loadFriendLists();
    } else if(panel === 'friendQueries'){
        loadQueries();
    }
}


function createFriendItem(friend){
    const li = document.createElement('li');
    li.textContent = friend.username;
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
    fetch('database/getFriends.php?id=' + friendId,
    )
        .then(res => res.json())
        .then(res => {
            const friendList = document.getElementById('friendList');
            renderList(friendList, res, createFriendItem, 'No friends found.');
        })
        .catch(err => {
            document.getElementById('friendList').innerHTML = `<li>Error loading friends.</li>`;
        });
}

function saveQuerytoProfile(id, message){
    const formData = new FormData();
    formData.append('id', id);
    fetch('database/saveQuery.php?id=' + friendId, {
        method: 'POST',
        body: formData 
    })
    .then(r => r.json())
    .then(data =>{
        if(data.success){
            message.style.color = 'green';
            message.textContent = data.message;
        }
        else {
            message.style.color = 'red';
            message.textContent = data.message;
        }
    })
    .catch(err=>{
        message.style.color = 'red';
        message.textContent = "Error: " + err;
    })
}

function makeButton(label, handler){
    const btn = document.createElement('button');
    btn.type = "button";
    btn.textContent = label;
    btn.addEventListener('click', handler);
    return btn;
};

//to be implemented
// function useQuery(){

// }

function goBack(){
    window.location.href = "profile.php";
}

function createQueryItem(query) {
    const { id: queryId, name: queryName } = query;
    const li = document.createElement('li');

    const divInfo = document.createElement('div');
    divInfo.className = "queryInfo";
    const spanId = document.createElement('span');
    spanId.textContent = queryId;
    const spanName = document.createElement('span');
    spanName.textContent = queryName;
    divInfo.append(spanId, spanName);
    
    const divButtons = document.createElement('div');
    divButtons.className = "queryButtons";
    const message = document.createElement('div');
    message.textContent = '';

    const btnUse = makeButton('Use', () => useQuery(queryId, message));

    const btnSaveToProfile = makeButton('Save to profile', () => saveQuerytoProfile(queryId, message));

    divButtons.append(message, btnUse, btnSaveToProfile);
    li.append(divInfo, divButtons);

    return li;
}

function loadQueries(){
    fetch('database/getQueries.php?id=' + friendId)
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
