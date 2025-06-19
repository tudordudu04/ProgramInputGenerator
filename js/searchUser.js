function addFriendFunction(name, button, div3){
    const formData = new FormData();
    formData.append('username', name);
    formData.append('request', "true");
    fetch('../database/addFriend.php', {
            method: 'POST',
            body: formData
        })
    .then(r => r.json())
    .then(data => {
        button.style.display = 'none';
        div3.textContent = data.message;
        div3.style.color = data.success ? "green" : "red";
        div3.style.display = 'block';
    })
    .catch(error => {
        div3.textContent = "Error: " + error;
        div3.style.color = "red";
        div3.style.display = 'block';
    });
}

function runScript() {
  fetch('../database/runScript.php', { method: 'POST' })
    .then(response => response.text())
    .then(data => {
      document.getElementById('result').innerHTML = data;
    })
    .catch(error => {
      document.getElementById('result').innerHTML = "Error: " + error;
    });
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

function createSearchItem(name) {
    const div = document.createElement('div');
    const li = document.createElement('li');
    li.textContent = name;
    const div2 = document.createElement('div');
    const button = document.createElement('button')
    const div3 = document.createElement('div');
    div2.style.display = 'flex';
    div2.style.alignItems = 'center';
    div3.style.display = 'none';
    button.textContent = 'Add Friend';
    button.addEventListener('click', (e) => {
        e.preventDefault();
        addFriendFunction(name, button, div3);
    });
    div2.appendChild(button);
    div2.appendChild(div3);
    div.appendChild(li);
    div.appendChild(div2);
    return div;
}

document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('queryBox');
    const resultsList = document.getElementById('results');

    input.addEventListener('keydown', function(event){
        if (event.key === 'Enter') {
            event.preventDefault();
        }
    });

    input.addEventListener('input', function() {
        const query = input.value.trim();
        if (query.length === 0) {
            resultsList.innerHTML = '';
            return;
        }

        const formData = new FormData();
        formData.append('query', query);

        fetch('../database/searchUsers.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(results => {
            renderList(resultsList, results, createSearchItem, 'No users found.');
        })
        .catch(err => {
            resultsList.innerHTML = '<li>Error loading results</li>' + err;
        });
    });

    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !resultsList.contains(e.target)) {
            resultsList.innerHTML = '';
        }
    });
});