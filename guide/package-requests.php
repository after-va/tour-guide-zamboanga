<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: index.php");
    exit();
}

require_once "../php/CustomPackage.php";
require_once "../php/Notification.php";
require_once "../php/Booking.php";

$customPackage = new CustomPackage();
$notification = new Notification();
$booking = new Booking();

// Handle accept request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'accept') {
    if ($customPackage->acceptRequest($_POST['request_ID'], $_SESSION['user_id'])) {
        // Get request details
        $request = $customPackage->getRequestById($_POST['request_ID']);
        
        // Send notification to tourist
        $notification->createNotification(
            $request['tourist_ID'],
            'package_accepted',
            'Package Request Accepted!',
            'Your custom package request "' . $request['request_title'] . '" has been accepted by ' . $_SESSION['full_name'] . '. Please proceed to payment.',
            'tourist/my-requests.php?id=' . $_POST['request_ID']
        );
        
        $success = "Request accepted successfully! Tourist has been notified.";
    } else {
        $error = "Failed to accept request.";
    }
}

// Handle reject request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'reject') {
    if ($customPackage->rejectRequest($_POST['request_ID'], $_SESSION['user_id'], $_POST['rejection_reason'])) {
        // Get request details
        $request = $customPackage->getRequestById($_POST['request_ID']);
        
        // Send notification to tourist
        $notification->createNotification(
            $request['tourist_ID'],
            'package_rejected',
            'Package Request Declined',
            'Your custom package request "' . $request['request_title'] . '" has been declined. Reason: ' . $_POST['rejection_reason'],
            'tourist/my-requests.php?id=' . $_POST['request_ID']
        );
        
        $success = "Request rejected. Tourist has been notified.";
    } else {
        $error = "Failed to reject request.";
    }
}

// Handle add message
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_message') {
    if ($customPackage->addRequestMessage($_POST['request_ID'], $_SESSION['user_id'], $_POST['message_text'])) {
        $request = $customPackage->getRequestById($_POST['request_ID']);
        
        // Notify tourist
        $notification->createNotification(
            $request['tourist_ID'],
            'package_message',
            'New Message on Package Request',
            $_SESSION['full_name'] . ' sent you a message regarding your package request.',
            'tourist/my-requests.php?id=' . $_POST['request_ID']
        );
        
        $success = "Message sent successfully!";
    } else {
        $error = "Failed to send message.";
    }
}

$pendingRequests = $customPackage->getRequestsByGuide($_SESSION['user_id'], 'pending');
$acceptedRequests = $customPackage->getRequestsByGuide($_SESSION['user_id'], 'accepted');
$allRequests = $customPackage->getRequestsByGuide($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Package Requests - Tour Guide System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        nav { background: #333; padding: 10px; margin-bottom: 20px; }
        nav a { color: white; text-decoration: none; margin-right: 15px; }
        nav a:hover { text-decoration: underline; }
        .success { background: #d4edda; color: #155724; padding: 10px; margin: 10px 0; border-radius: 5px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0; border-radius: 5px; }
        .tabs { margin: 20px 0; border-bottom: 2px solid #ddd; }
        .tab { display: inline-block; padding: 10px 20px; cursor: pointer; background: #f0f0f0; 
               border: 1px solid #ddd; border-bottom: none; margin-right: 5px; }
        .tab.active { background: white; border-bottom: 2px solid white; margin-bottom: -2px; }
        .tab-content { display: none; padding: 20px 0; }
        .tab-content.active { display: block; }
        .request-card { border: 1px solid #ddd; padding: 15px; margin: 15px 0; border-radius: 5px; background: #f9f9f9; }
        .request-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .request-title { font-size: 18px; font-weight: bold; color: #333; }
        .badge { padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; }
        .badge-pending { background: #ffc107; color: black; }
        .badge-accepted { background: #28a745; color: white; }
        .badge-rejected { background: #dc3545; color: white; }
        .badge-completed { background: #6c757d; color: white; }
        .request-details { margin: 10px 0; }
        .request-details p { margin: 5px 0; }
        .btn { padding: 8px 15px; margin: 5px; cursor: pointer; border: none; border-radius: 4px; text-decoration: none; display: inline-block; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-info { background: #17a2b8; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .modal { display: none; position: fixed; z-index: 1; left: 0; top: 0; 
                 width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); }
        .modal-content { background-color: #fefefe; margin: 5% auto; padding: 20px; 
                         border: 1px solid #888; width: 80%; max-width: 700px; border-radius: 5px; max-height: 80vh; overflow-y: auto; }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        .close:hover { color: black; }
        .form-group { margin: 15px 0; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { 
            width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;
        }
        .spot-list { list-style: none; padding: 0; }
        .spot-item { background: white; padding: 10px; margin: 5px 0; border-left: 3px solid #007bff; }
        .priority-high { border-left-color: #dc3545; }
        .priority-medium { border-left-color: #ffc107; }
        .priority-low { border-left-color: #28a745; }
        .message-thread { max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; margin: 10px 0; background: white; }
        .message { padding: 10px; margin: 5px 0; border-radius: 5px; }
        .message-guide { background: #e3f2fd; text-align: right; }
        .message-tourist { background: #f0f0f0; text-align: left; }
        .message-sender { font-weight: bold; font-size: 12px; }
        .message-time { font-size: 11px; color: #666; }
    </style>
</head>
<body>
    <h1>Package Requests</h1>
    <p>Welcome, <?php echo $_SESSION['full_name']; ?>!</p>
    
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="my-schedules.php">My Schedules</a>
        <a href="my-bookings.php">My Bookings</a>
        <a href="my-packages.php">My Packages</a>
        <a href="package-requests.php">Package Requests</a>
        <a href="logout.php">Logout</a>
    </nav>
    
    <?php if (isset($success)): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="tabs">
        <div class="tab active" onclick="showTab('pending')">Pending (<?php echo count($pendingRequests); ?>)</div>
        <div class="tab" onclick="showTab('accepted')">Accepted (<?php echo count($acceptedRequests); ?>)</div>
        <div class="tab" onclick="showTab('all')">All Requests (<?php echo count($allRequests); ?>)</div>
    </div>
    
    <!-- Pending Requests Tab -->
    <div id="pending" class="tab-content active">
        <h2>Pending Requests</h2>
        <?php if (count($pendingRequests) > 0): ?>
            <?php foreach ($pendingRequests as $req): ?>
                <div class="request-card">
                    <div class="request-header">
                        <div class="request-title"><?php echo htmlspecialchars($req['request_title']); ?></div>
                        <span class="badge badge-pending">PENDING</span>
                    </div>
                    <div class="request-details">
                        <p><strong>Tourist:</strong> <?php echo htmlspecialchars($req['tourist_name']); ?></p>
                        <p><strong>Contact:</strong> <?php echo htmlspecialchars($req['tourist_email']); ?> | <?php echo htmlspecialchars($req['tourist_phone']); ?></p>
                        <p><strong>Preferred Date:</strong> <?php echo $req['preferred_date'] ? date('F j, Y', strtotime($req['preferred_date'])) : 'Flexible'; ?></p>
                        <p><strong>Duration:</strong> <?php echo htmlspecialchars($req['preferred_duration']); ?></p>
                        <p><strong>Number of PAX:</strong> <?php echo $req['number_of_pax']; ?></p>
                        <p><strong>Budget Range:</strong> <?php echo htmlspecialchars($req['budget_range']); ?></p>
                        <?php if ($req['special_requirements']): ?>
                            <p><strong>Special Requirements:</strong> <?php echo nl2br(htmlspecialchars($req['special_requirements'])); ?></p>
                        <?php endif; ?>
                        <p><strong>Requested:</strong> <?php echo date('F j, Y g:i A', strtotime($req['created_at'])); ?></p>
                    </div>
                    <div>
                        <button class="btn btn-info" onclick="viewRequestDetails(<?php echo $req['request_ID']; ?>)">View Details</button>
                        <button class="btn btn-success" onclick="acceptRequest(<?php echo $req['request_ID']; ?>, '<?php echo htmlspecialchars($req['request_title']); ?>')">Accept</button>
                        <button class="btn btn-danger" onclick="rejectRequest(<?php echo $req['request_ID']; ?>, '<?php echo htmlspecialchars($req['request_title']); ?>')">Reject</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No pending requests at the moment.</p>
        <?php endif; ?>
    </div>
    
    <!-- Accepted Requests Tab -->
    <div id="accepted" class="tab-content">
        <h2>Accepted Requests</h2>
        <?php if (count($acceptedRequests) > 0): ?>
            <?php foreach ($acceptedRequests as $req): ?>
                <div class="request-card">
                    <div class="request-header">
                        <div class="request-title"><?php echo htmlspecialchars($req['request_title']); ?></div>
                        <span class="badge badge-accepted">ACCEPTED</span>
                    </div>
                    <div class="request-details">
                        <p><strong>Tourist:</strong> <?php echo htmlspecialchars($req['tourist_name']); ?></p>
                        <p><strong>Preferred Date:</strong> <?php echo $req['preferred_date'] ? date('F j, Y', strtotime($req['preferred_date'])) : 'Flexible'; ?></p>
                        <p><strong>PAX:</strong> <?php echo $req['number_of_pax']; ?> | <strong>Budget:</strong> <?php echo htmlspecialchars($req['budget_range']); ?></p>
                    </div>
                    <div>
                        <button class="btn btn-info" onclick="viewRequestDetails(<?php echo $req['request_ID']; ?>)">View Details & Messages</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No accepted requests yet.</p>
        <?php endif; ?>
    </div>
    
    <!-- All Requests Tab -->
    <div id="all" class="tab-content">
        <h2>All Requests</h2>
        <?php if (count($allRequests) > 0): ?>
            <?php foreach ($allRequests as $req): ?>
                <div class="request-card">
                    <div class="request-header">
                        <div class="request-title"><?php echo htmlspecialchars($req['request_title']); ?></div>
                        <span class="badge badge-<?php echo $req['request_status']; ?>"><?php echo strtoupper($req['request_status']); ?></span>
                    </div>
                    <div class="request-details">
                        <p><strong>Tourist:</strong> <?php echo htmlspecialchars($req['tourist_name']); ?></p>
                        <p><strong>Date:</strong> <?php echo $req['preferred_date'] ? date('F j, Y', strtotime($req['preferred_date'])) : 'Flexible'; ?> | 
                           <strong>PAX:</strong> <?php echo $req['number_of_pax']; ?></p>
                        <p><strong>Status:</strong> <?php echo ucfirst($req['request_status']); ?> on <?php echo date('F j, Y', strtotime($req['updated_at'])); ?></p>
                    </div>
                    <div>
                        <button class="btn btn-info" onclick="viewRequestDetails(<?php echo $req['request_ID']; ?>)">View Details</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No requests yet.</p>
        <?php endif; ?>
    </div>
    
    <!-- View Details Modal -->
    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('detailsModal').style.display='none'">&times;</span>
            <div id="detailsContent"></div>
        </div>
    </div>
    
    <!-- Accept Modal -->
    <div id="acceptModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('acceptModal').style.display='none'">&times;</span>
            <h2>Accept Package Request</h2>
            <p id="acceptMessage"></p>
            <form method="POST">
                <input type="hidden" name="action" value="accept">
                <input type="hidden" name="request_ID" id="accept_request_ID">
                <p>By accepting this request, the tourist will be notified and can proceed to payment.</p>
                <button type="submit" class="btn btn-success">Confirm Accept</button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('acceptModal').style.display='none'">Cancel</button>
            </form>
        </div>
    </div>
    
    <!-- Reject Modal -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('rejectModal').style.display='none'">&times;</span>
            <h2>Reject Package Request</h2>
            <p id="rejectMessage"></p>
            <form method="POST">
                <input type="hidden" name="action" value="reject">
                <input type="hidden" name="request_ID" id="reject_request_ID">
                <div class="form-group">
                    <label>Reason for Rejection:</label>
                    <textarea name="rejection_reason" rows="4" required placeholder="Please provide a reason for declining this request..."></textarea>
                </div>
                <button type="submit" class="btn btn-danger">Confirm Reject</button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('rejectModal').style.display='none'">Cancel</button>
            </form>
        </div>
    </div>
    
    <script>
        function showTab(tabName) {
            const tabs = document.querySelectorAll('.tab');
            const contents = document.querySelectorAll('.tab-content');
            
            tabs.forEach(tab => tab.classList.remove('active'));
            contents.forEach(content => content.classList.remove('active'));
            
            event.target.classList.add('active');
            document.getElementById(tabName).classList.add('active');
        }
        
        function viewRequestDetails(requestID) {
            // Load request details via AJAX
            fetch('get-request-details.php?id=' + requestID)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('detailsContent').innerHTML = data;
                    document.getElementById('detailsModal').style.display = 'block';
                })
                .catch(error => {
                    alert('Error loading request details');
                });
        }
        
        function acceptRequest(requestID, title) {
            document.getElementById('accept_request_ID').value = requestID;
            document.getElementById('acceptMessage').textContent = 'Are you sure you want to accept the request: "' + title + '"?';
            document.getElementById('acceptModal').style.display = 'block';
        }
        
        function rejectRequest(requestID, title) {
            document.getElementById('reject_request_ID').value = requestID;
            document.getElementById('rejectMessage').textContent = 'You are about to reject the request: "' + title + '"';
            document.getElementById('rejectModal').style.display = 'block';
        }
        
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>
