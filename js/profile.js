let profileData;

fetch('../database/getProfile.php', {
    credentials: 'include'
    })
    .then(r => r.json())
    .then(profile => {
        profileData = profile;
        loadProfileToDisplay(profile);
});



function showPanel(panel) {
    const panels = ['profile', 'friends', 'queries', 'results', 'button', 'createTicket', 'reviewTicket', 'users'];
    panels.forEach(function(name) {
        aux = document.getElementById(name + '-panel');
        if(aux)
            aux.style.display = (name === panel) ? 'flex' : 'none';
    });
    if(panel === 'friends'){
        loadFriendLists();
    } else if(panel === 'queries'){
        loadQueries();
    } else if(panel === 'createTicket'){
        loadTickets();
    } else if(panel === 'reviewTicket'){
        loadTickets('others');
    } else if(panel === 'users'){
        loadUsers();
    }
}


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
                    document.getElementById('messageProfileSave').textContent = '';
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

// function blockUser(id, message){

// }

function submitTicket(event){
    const form = document.getElementById('supportTicketForm');
    const messageDiv = document.getElementById('messageSubmitTicket');
    event.preventDefault();

    const formData = new FormData(form);
    fetch('../database/submitTicket.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if(data.success){
            form.reset();
            loadTickets();
            messageDiv.textContent = 'Ticket submitted succesfully';
            messageDiv.style.color = 'green';
        } else {
            messageDiv.textContent = 'Couldn\'t submit ticket: ' + data.message;
            messageDiv.style.color = 'red';
        }
    })
    .catch(err => {
        messageDiv.textContent = 'Error: ' + err;
        messageDiv.style.color = 'red';
    })
}

function viewProfile(id, message){
    window.location.href = `friendProfile.php?id=${encodeURIComponent(id)}`;
}

function createFriendItem(friend){
    const li = document.createElement('li');
    const divUsername = document.createElement('div');
    divUsername.className = "username";
    divUsername.textContent = friend.username;

    const divButtons = document.createElement('div');
    divButtons.className = "friendButtons";
    const message = document.createElement('div');
    message.textContent = '';

    const btnViewProfile = makeButton('View Profile', () => viewProfile(friend.id, message));
    const btnRemoveFriend = makeButton('Remove', () => friendListActions(friend.id, 'remove', message));
    // const btnBlockUser = makeButton('Block User', () => blockUser(friend.id, message)); //vedem daca mai implementez si asta :PP
    divButtons.append(message, btnViewProfile, btnRemoveFriend);
    // divButtons.append(btnBlockUser);
    
    li.append(divUsername);
    li.append(divButtons);
    return li;
}

function friendListActions(id, action, message){
    const formData = new FormData();
    formData.append('id', id);
    formData.append('action', action); 
    fetch('../database/friendListActions.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if(data.success)
            loadFriendLists();
        else {
            message.style.color = 'red';
            message.textContent = data.message;
        }
    })
    .catch(err=>{
        message.style.color = 'red';
        message.textContent = "Error: " + err;
    });
}

function createFriendRequestItem(friendRequest){
    const li = document.createElement('li');
    const divUsername = document.createElement('div');
    divUsername.className = "username";
    divUsername.textContent = friendRequest.username;

    const divButtons = document.createElement('div');
    divButtons.className = "friendRequestButtons";
    const message = document.createElement('div');
    message.textContent = '';

    const btnAcceptProfile = makeButton('Accept', () => friendListActions(friendRequest.id, 'accept', message));
    const btnDenyFriend = makeButton('Deny', () => friendListActions(friendRequest.id, 'deny', message));
    divButtons.append(message, btnAcceptProfile, btnDenyFriend);
    
    li.append(divUsername);
    li.append(divButtons);
    return li;
}
function ticketListActions(id, action, message){
    const formData = new FormData();
    formData.append('id', id);
    formData.append('action', action); 
    fetch('../database/ticketListActions.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if(data.success)
            loadTickets('others');
        else {
            message.style.color = 'red';
            message.textContent = data.message;
        }
    })
    .catch(err=>{
        message.style.color = 'red';
        message.textContent = "Error: " + err;
    });
}
function viewTicket(ticket) {
    const oldViewer = document.getElementById('ticketViewer');
    if (oldViewer) oldViewer.remove();

    const viewer = document.createElement('div');
    viewer.id = "ticketViewer";

    const box = document.createElement('div');
    box.className = "ticketInnerBox";

    box.innerHTML = `
        <h2>Ticket #${ticket.id}</h2>
        <p><strong>Title:</strong> ${escapeHTML(ticket.title)}</p>
        <p><strong>Type:</strong> ${escapeHTML(ticket.type)}</p>
        <p><strong>Status:</strong> ${escapeHTML(ticket.status)}</p>
        <p><strong>Description:</strong><br>${escapeHTML(ticket.body).replace(/\n/g, '<br>')}</p>
        <button style="margin-top: 1rem;" onclick="document.getElementById('ticketViewer').remove()">Close</button>
    `;

    viewer.onclick = function(e) {
        if (e.target === viewer) viewer.remove();
    }

    viewer.appendChild(box);
    document.body.appendChild(viewer);
}
function escapeHTML(str) {
    return String(str)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#39;");
}
function createTicketItem(ticket){
    const li = document.createElement('li');
    const divInfo = document.createElement('div');
    divInfo.className = "ticketInfo";

    const ticketName = document.createElement('div');
    ticketName.textContent = ticket.title;
    divInfo.append(ticketName);

    const ticketType = document.createElement('div');
    ticketType.textContent = ticket.type;
    divInfo.append(ticketType);

    const status = document.createElement('div');
    status.textContent = ticket.status;
    divInfo.append(status);

    const divButtons = document.createElement('div');
    divButtons.className = "ticketButtons";
    const message = document.createElement('div');
    message.textContent = '';

    const btnViewTicket = makeButton('View Ticket', () => viewTicket(ticket));
    divButtons.append(message);
    divButtons.append(btnViewTicket);

    if(ticket.isAdmin === true){
        const ticketUser = document.createElement('div');
        ticketUser.textContent = "From: " + ticket.username;
        divInfo.append(ticketUser);

        const btnResolveTicket = makeButton('Solved', () => ticketListActions(ticket.id, 'resolved', message));
        const btnCloseTicket = makeButton('Close', () => ticketListActions(ticket.id, 'closed', message));
        divButtons.append(btnResolveTicket, btnCloseTicket);
    }
    
    li.append(divInfo);
    li.append(divButtons);
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

function loadTickets(forWhom = 'myself'){
    const formAux = new FormData();
    formAux.append('forWhom', forWhom)
    fetch('../database/getTickets.php',{
        method: 'POST',
        body: formAux
    })
    .then(r => r.json())
    .then(data => {
        let ticketList;
        if(forWhom !== "myself")
            ticketList = document.getElementById('reviewTicketList');
        else   
            ticketList = document.getElementById('submittedTicketList');
        renderList(ticketList, data, createTicketItem, 'No tickets found.');
    })
    .catch(err => {
        document.getElementById('reviewTicketList').innerHTML = '<li>Error loading tickets.</li>';
        document.getElementById('submittedTicketList').innerHTML = '<li>Error loading tickets.</li>';
    })
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

function renameQuery(id, name, message){
    const formData = new FormData();
    formData.append('id', id);
    formData.append('name', name);
    fetch('../database/renameQuery.php', {
        method: 'POST',
        body: formData 
    })
    .then(r => r.json())
    .then(data =>{
        if(data.success)
            loadQueries();
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
function deleteQuery(id, message){
    const formData = new FormData();
    formData.append('id', id);
    fetch('../database/deleteQuery.php', {
        method: 'POST',
        body: formData 
    })
    .then(r => r.json())
    .then(data =>{
        if(data.success)
            loadQueries();
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
    btn.innerText = label;
    btn.addEventListener('click', handler);
    return btn;
};

function createQueryItem(query) {
    const { id: queryId, name: queryName } = query;
    const li = document.createElement('li');

    const divInfo = document.createElement('div');
    divInfo.className = "queryInfo";
    const spanId = document.createElement('span');
    spanId.innerText = queryId;
    const spanName = document.createElement('span');
    spanName.innerText = queryName;
    divInfo.append(spanId, spanName);

    //butoane + mesaj rezultat
    //de adaugat clase pentru css
    const divButtons = document.createElement('div');
    divButtons.className = "queryButtons";
    const message = document.createElement('div');
    message.textContent = '';

    const btnUse = makeButton('Use', () => useQuery(queryId, message));

    const dropdownRename = document.createElement('div');
    const input = document.createElement('input');
    input.type = 'text';
    input.style.display = 'none';
    input.placeholder = 'Name...';
    input.autocomplete = 'off';

    input.addEventListener('keydown', function(event){
        if (event.key === 'Enter') {
            event.preventDefault();
            const newName = input.value.trim();
            if (newName.length === 0) return;
            renameQuery(queryId, newName, message);
        }
    });

    const btnRename = makeButton('Rename', () => {
        if (btnRename.textContent === "Rename") {
            input.style.display = 'block';
            btnRename.innerText = "Cancel";
            input.focus();
        } else {
            input.style.display = 'none';
            btnRename.innerText = "Rename";
        }
    });

    dropdownRename.append(btnRename, input);

    const btnDelete = makeButton('Delete', () => deleteQuery(queryId, message));

    divButtons.append(btnUse, dropdownRename, btnDelete);
    li.append(divInfo, divButtons);

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

function cancelEdit(){
    document.getElementById('profileDisplay').style.display = '';
    document.getElementById('profileForm').style.display = 'none';
    document.getElementById('profileDisplayButtons').style.display = '';
    document.getElementById('formEditProfileButtons').style.display = 'none';
    document.getElementById('messageProfileSave').textContent = '';
    loadProfileToDisplay(profileData);
}

function editProfile(){
    document.getElementById('profileDisplay').style.display = 'none';
    document.getElementById('profileForm').style.display = 'flex';
    document.getElementById('profileDisplayButtons').style.display = 'none';
    document.getElementById('formEditProfileButtons').style.display = 'block';
    document.getElementById('profileSaveError').textContent = '';
    loadProfileToForm(profileData);
}

//ar trebui sa verific daca chiar se schimba ceva fata de cum era inainte
//de update ca sa nu mai bag mesaj de updated succesfully la fiecare save 
function saveProfile(event){
    const messageDiv = document.getElementById('messageProfileSave');
    const errorDiv = document.getElementById('profileSaveError');
    event.preventDefault();

    const formData = new FormData(document.getElementById('profileForm'));
    fetch('../database/saveProfile.php', {
        method: 'POST',
        body: formData
    })
    .then(data => {
        if (!data.ok) {
            throw new Error(data.status);
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
}

function saveProfilePhoto(event){
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
}