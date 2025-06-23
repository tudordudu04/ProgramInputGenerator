<?php
    $additionalCss = '<link rel="alternative stylesheet" href="css/profile.css">';
    $scriptSource = '<script src="js/friendProfile.js"></script>'; 
?>
<div class="container">
    <?php include '../includes/header.php'; ?>
    <div class="main">
        <div class="navbar">
            <button onclick="showPanel('friendProfile')">Profile</button>
            <button onclick="showPanel('friendFriends')">Friend List</button>
            <button onclick="showPanel('friendQueries')">Saved Queries</button>
            <button onclick="goBack()">Go Back</button>
        </div>
        <div class="workspace" id="workspace">
            <div class="panel" id="friendProfile-panel">
                <div class="left-side">
                    <div id="profileDisplay">
                        <h2>PROFILE</h2>
                        <ul class="settings">
                            <li><strong>Username:</strong> <span id="displayUsername"></span></li>
                            <li><strong>Email:</strong> <span id="displayEmail"></span></li>
                            <li><strong>First Name:</strong> <span id="displayFirstName"></span></li>
                            <li><strong>Last Name:</strong> <span id="displayLastName"></span></li>
                            <li><strong>Telephone Number:</strong> <span id="displayPhoneNumber"></span></li>
                            <li><strong>Address:</strong> <span id="displayAddress"></span></li>
                            <li><strong>Country:</strong> <span id="displayCountry"></span></li>
                            <li><strong>City:</strong> <span id="displayCity"></span></li>
                        </ul>
                    </div>
                </div>
                <div class="right-side">
                    <div class="card">
                        <div class="image-container">
                            <img src="" class="image" id="profilePhoto" alt="Profile Photo">
                        </div>
                        <p id="profileName">FirstName LastName</p>
                        <p id="profileEmail">Email</p>
                    </div>
                    <div id="profileDisplayButtons">
                        <button type="button" onclick="reportProfile()">Report Profile</button>
                        <div id="messageProfileReport"></div>
                    </div>
                </div>
            </div>

            <div class="panel" id="friendFriends-panel" style="display:none;">
                <div class="list">
                    <h2>Friend List</h2>
                    <ul id="friendList">
                    </ul>
                </div>
            </div>
        
            <div class="panel" id="friendQueries-panel" style="display:none;">
                <h2>Saved Queries</h2>
                <ul id="queriesList">
                </ul>
            </div>
        </div>
    </div>    
    <?php include '../includes/footer.php'; ?>
</div>
