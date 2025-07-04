<?php
    $additionalCss = '<link rel="alternative stylesheet" href="css/profile.css">';
    $scriptSource = '<script src="js/profile.js"></script>';
    include '../database/isAdmin.php';
?>
<?php include '../includes/markup.php'; ?>
<div class="container">
    <?php include '../includes/header.php'; ?>
    <div class="main">
        <div class="navbar">
            <button onclick="showPanel('profile')">Profile</button>
            <button onclick="showPanel('friends')">Friend List</button>
            <button onclick="showPanel('queries')">Saved Queries</button>
            <button onclick="showPanel('createTicket')">Support Tickets</button>
            <?php if($isAdmin):?>
                <button onclick="showPanel('reviewTicket')">Tickets</button>
                <button onclick="showPanel('users')">User List</button> 
            <?php endif; ?>
        </div>
        <div class="workspace" id="workspace">
            <div class="panel" id="profile-panel">
                <div class="left-side">
                    <div id="profileDisplay">
                        <h2>PROFILE</h2>
                        <ul class="settings">
                            <li><strong>Hidden:</strong> <span id="displayHidden"></span></li>
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
                    <form class="settings" id="profileForm" onsubmit="saveProfile(event)" >
                        <h2>Edit Profile</h2>
                        <div>
                            <label for="hidden">Hidden</label>
                            <input type="checkbox" id="hidden" name="hidden">
                        </div>
                        <span>
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username" placeholder="enter username">
                        </span>
                        <span>
                            <label for="email">Email</label>
                            <input type="text" name="email" id="email" placeholder="enter email">
                        </span>
                        <div class="doubleInput">
                            <span>
                                <label for="firstName">First Name</label>
                                <input type="text" name="firstName" id="firstName" placeholder="enter first name">
                            </span>
                            <span>
                                <label for="lastName">Last Name</label>
                                <input type="text" name="lastName" id="lastName" placeholder="enter last name">
                            </span>
                        </div>

                        <span>
                            <label for="phoneNumber">Telephone Number</label>
                            <input type="text" name="phoneNumber" id="phoneNumber" placeholder="enter telephone number">
                        </span>
                        <span>
                            <label for="address">Address</label>
                            <input type="text" name="address" id="address" placeholder="enter address">
                        </span>
                        <div class="doubleInput">
                            <span>
                                <label for="country">Country</label>
                                <input type="text" name="country" id="country" placeholder="enter country">
                            </span>
                            <span>
                                <label for="city">City</label>
                                <input type="text" name="city" id="city" placeholder="enter city">
                            </span>
                        </div>
                    </form>
                </div>
                <div class="right-side">
                    <div class="card">
                        <div class="image-container">
                        <img src="profilePhotos/placeholder.png" class="image" id="profilePhoto" alt="Profile Photo">
                        <div class="overlay">Change Photo</div>
                        <input type="file" accept="image/*" class="file-input" onchange="saveProfilePhoto(event)">
                        </div>
                        <p id="profileName">FirstName LastName</p>
                        <p id="profileEmail">Email</p>
                    </div>
                    <div id="profileDisplayButtons">
                        <button type="button" onclick="editProfile()">Edit</button>
                        <button type="button" onclick="deleteAccount()" >Delete Account</button>
                        <div id="messageProfileSave"></div>
                        <div id="messageProfileDelete"></div>
                    </div>
                    <div id="formEditProfileButtons">
                        <button type="submit" form="profileForm">Save Profile</button>
                        <button type="button" onclick="cancelEdit()">Cancel Edit</button>
                        <div id="profileSaveError"></div>
                    </div>
                </div>
            </div>

            <div class="panel" id="friends-panel" style="display:none;">
                <div class="list">
                    <h2>Friend List</h2>
                    <ul id="friendList">
                    </ul>
                </div>
                <div class="requests">
                    <h2>Friend Requests</h2>
                    <ul id="friendRequests">
                    </ul>
                </div>
            </div>
        
            <div class="panel" id="queries-panel" style="display:none;">
                <h2>Saved Queries</h2>
                <ul id="queriesList">
                </ul>
            </div>
            <div class="panel" id="createTicket-panel" style="display:none;">
                <h2>Support Tickets</h2>
                <form id="supportTicketForm" onsubmit="submitTicket(event)">
                    <label for="ticketTitle">Title:</label>
                    <input type="text" id="ticketTitle" name="ticketTitle" required>

                    <label for="ticketReason">Reason:</label>
                    <select id="ticketReason" name="ticketReason" required>
                        <option value="" disabled selected>Select a reason</option>
                        <option value="bug">Bug Report</option>
                        <option value="account">Account Issue</option>
                        <option value="suggestion">Suggestion</option>
                    </select>

                    <label for="ticketBody">Complaint:</label>
                    <textarea id="ticketBody" name="ticketBody" rows="6" required></textarea>

                    <button type="submit">Submit Ticket</button>
                    <div id="messageSubmitTicket"></div>
                </form>
                <h2>Your Tickets</h2>
                <ul id="submittedTicketList">
                        
                </ul>   
            </div>
            <?php if($isAdmin):?>
                <div class="panel" id="reviewTicket-panel" style="display:none;">
                    <div class="list">
                        <h2>Ticket List</h2>
                        <ul id="reviewTicketList">
                        
                        </ul>
                    </div>
                </div>
                <div class="panel" id="users-panel" style="display:none;">
                    <div class="list">
                        <h2>User List</h2>
                        <ul id="usersList">

                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</div>