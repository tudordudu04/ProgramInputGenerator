function addFriendFunction(name){
    const formData = new FormData();
        formData.append('username', name);
    fetch('../database/addFriend.php', {
            method: 'POST',
            body: formData
        });
    //Add error handling in case of request not being successful 
}

document.addEventListener('DOMContentLoaded', function() {
    const input = document.querySelector('.search input[type="text"]');
    const resultsList = document.getElementById('results');

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
            resultsList.innerHTML = '';
            if (results.length === 0) {
                resultsList.innerHTML = '<li>No results</li>';
            } else {
                results.forEach(name => {
                    const div = document.createElement('div');
                    const li = document.createElement('li');
                    li.textContent = name;
                    const button = document.createElement('button')
                    button.textContent = 'Add Friend';
                    button.addEventListener('click', (e) => {
                        e.preventDefault();
                        addFriendFunction(name);
                    });
                    
                    div.appendChild(li);
                    div.appendChild(button);
                    resultsList.appendChild(div);
                });
            }
        })
        .catch(err => {
            resultsList.innerHTML = '<li>Error loading results</li>';
        });
    });

    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !resultsList.contains(e.target)) {
            resultsList.innerHTML = '';
        }
    });
});