<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: index.php");
    exit();
}

require_once "../php/CustomPackage.php";
require_once "../php/Payment.php";
require_once "../php/Notification.php";

$customPackage = new CustomPackage();
$payment = new Payment();
$notification = new Notification();

// Handle cancel request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'cancel') {
    if ($customPackage->cancelRequest($_POST['request_ID'], $_SESSION['user_id'])) {
        $request = $customPackage->getRequestById($_POST['request_ID']);
        
        // Notify guide
        $notification->createNotification(
            $request['guide_ID'],
            'package_cancelled',
            'Package Request Cancelled',
            $_SESSION['full_name'] . ' has cancelled their package request: "' . $request['request_title'] . '"',
            'guide/package-requests.php'
        );
        
        $success = "Request cancelled successfully.";
    } else {
        $error = "Failed to cancel request.";
    }
}

// Handle add message
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_message') {
    if ($customPackage->addRequestMessage($_POST['request_ID'], $_SESSION['user_id'], $_POST['message_text'])) {
        $request = $customPackage->getRequestById($_POST['request_ID']);
        
        // Notify guide
        $notification->createNotification(
            $request['guide_ID'],
            'package_message',
            'New Message on Package Request',
            $_SESSION['full_name'] . ' sent you a message regarding a package request.',
            'guide/package-requests.php?id=' . $_POST['request_ID']
        );
        
        $success = "Message sent successfully!";
    } else {
        $error = "Failed to send message.";
    }
}

$myRequests = $customPackage->getRequestsByTourist($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Package Requests - Tour Guide System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        nav { background: #333; padding: 10px; margin-bottom: 20px; }
        nav a { color: white; text-decoration: none; margin-right: 15px; }
        nav a:hover { text-decoration: underline; }
        .success { background: #d4edda; color: #155724; padding: 10px; margin: 10px 0; border-radius: 5px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0; border-radius: 5px; }
        .request-card { border: 1px solid #ddd; padding: 20px; margin: 15px 0; border-radius: 8px; background: #f9f9f9; }
        .request-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .request-title { font-size: 20px; font-weight: bold; color: #333; }
        .badge { padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; }
        .badge-pending { background: #ffc107; color: black; }
        .badge-accepted { background: #28a745; color: white; }
        .badge-rejected { background: #dc3545; color: white; }
        .badge-cancelled { background: #6c757d; color: white; }
        .badge-completed { background: #17a2b8; color: white; }
        .request-details { margin: 10px 0; }
        .request-details p { margin: 8px 0; }
        .btn { padding: 10px 15px; margin: 5px; cursor: pointer; border: none; border-radius: 4px; text-decoration: none; display: inline-block; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-info { background: #17a2b8; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn:hover { opacity: 0.9; }
        .modal { display: none; position: fixed; z-index: 1; left: 0; top: 0; 
                 width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); }
        .modal-content { background-color: #fefefe; margin: 5% auto; padding: 20px; 
                         border: 1px solid #888; width: 80%; max-width: 700px; border-radius: 5px; max-height: 80vh; overflow-y: auto; }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        .close:hover { color: black; }
        .alert-info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #17a2b8; }
        .alert-success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #28a745; }
        .alert-danger { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #dc3545; }
        .message-thread { max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; margin: 10px 0; background: white; }
        .message { padding: 10px; margin: 5px 0; border-radius: 5px; }
        .message-tourist { background: #e3f2fd; text-align: right; }
        .message-guide { background: #f0f0f0; text-align: left; }
        .message-sender { font-weight: bold; font-size: 12px; }
        .message-time { font-size: 11px; color: #666; }
        .form-group { margin: 15px 0; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
    </style>
</head>
<body>
    <h1>My Package Requests</h1>
    <p>Welcome, <?php echo $_SESSION['full_name']; ?>!</p>
    
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="browse-tours.php">Browse Tours</a>
        <a href="browse-guides.php">Browse Guides</a>
        <a href="my-requests.php">My Requests</a>
        <a href="my-bookings.php">My Bookings</a>
        <a href="logout.php">Logout</a>
    </nav>
    
    <?php if (isset($success)): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div style="margin: 20px 0;">
        <a href="browse-guides.php" class="btn btn-primary">+ Request New Package</a>
    </div>
    
    <h2>Your Package Requests</h2>
    
    <?php if (count($myRequests) > 0): ?>
        <?php foreach ($myRequests as $req): ?>
            <div class="request-card">
                <div class="request-header">
                    <div class="request-title"><?php echo htmlspecialchars($req['request_title']); ?></div>
                    <span class="badge badge-<?php echo $req['request_status']; ?>"><?php echo strtoupper($req['request_status']); ?></span>
                </div>
                
                <div class="request-details">
                    <p><strong>Tour Guide:</strong> <?php echo htmlspecialchars($req['guide_name']); ?> 
                       (★ <?php echo number_format($req['guide_rating'], 1); ?>)</p>
                    <p><strong>Preferred Date:</strong> <?php echo $req['preferred_date'] ? date('F j, Y', strtotime($req['preferred_date'])) : 'Flexible'; ?></p>
                    <p><strong>Duration:</strong> <?php echo htmlspecialchars($req['preferred_duration']); ?></p>
                    <p><strong>PAX:</strong> <?php echo $req['number_of_pax']; ?> | 
                       <strong>Budget:</strong> <?php echo htmlspecialchars($req['budget_range']); ?></p>
                    <?php if ($req['total_spots'] > 0): ?>
                        <p><strong>Requested Spots:</strong> <?php echo $req['total_spots']; ?> locations</p>
                    <?php endif; ?>
                    <p><strong>Requested:</strong> <?php echo date('F j, Y g:i A', strtotime($req['created_at'])); ?></p>
                    
                    <?php if ($req['request_status'] == 'pending'): ?>
                        <div class="alert-info">
                            <strong>⏳ Waiting for Guide Response</strong><br>
                            Your request is pending. The guide will review and respond soon.
                        </div>
                    <?php elseif ($req['request_status'] == 'accepted'): ?>
                        <div class="alert-success">
                            <strong>✓ Request Accepted!</strong><br>
                            The guide has accepted your request. Please proceed to payment to confirm your booking.
                        </div>
                    <?php elseif ($req['request_status'] == 'rejected'): ?>
                        <div class="alert-danger">
                            <strong>✗ Request Declined</strong><br>
                            <?php if ($req['rejection_reason']): ?>
                                Reason: <?php echo nl2br(htmlspecialchars($req['rejection_reason'])); ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div>
                    <button class="btn btn-info" onclick="viewRequestDetails(<?php echo $req['request_ID']; ?>)">View Details & Messages</button>
                    
                    <?php if ($req['request_status'] == 'pending'): ?>
                        <button class="btn btn-danger" onclick="cancelRequest(<?php echo $req['request_ID']; ?>, '<?php echo htmlspecialchars($req['request_title']); ?>')">Cancel Request</button>
                    <?php endif; ?>
                    
                    <?php if ($req['request_status'] == 'accepted'): ?>
                        <a href="payment-custom-package.php?request_id=<?php echo $req['request_ID']; ?>" class="btn btn-success">Proceed to Payment</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>You haven't made any package requests yet.</p>
        <a href="browse-guides.php" class="btn btn-primary">Browse Tour Guides</a>
    <?php endif; ?>
    
    <!-- View Details Modal -->
    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('detailsModal').style.display='none'">&times;</span>
            <div id="detailsContent"></div>
        </div>
    </div>
    
    <!-- Cancel Modal -->
    <div id="cancelModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('cancelModal').style.display='none'">&times;</span>
            <h2>Cancel Package Request</h2>
            <p id="cancelMessage"></p>
            <form method="POST">
                <input type="hidden" name="action" value="cancel">
                <input type="hidden" name="request_ID" id="cancel_request_ID">
                <p>Are you sure you want to cancel this request? This action cannot be undone.</p>
                <button type="submit" class="btn btn-danger">Confirm Cancel</button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('cancelModal').style.display='none'">Go Back</button>
            </form>
        </div>
    </div>
    
    <script>
        function viewRequestDetails(requestID) {
            fetch('get-request-details-tourist.php?id=' + requestID)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('detailsContent').innerHTML = data;
                    document.getElementById('detailsModal').style.display = 'block';
                })
                .catch(error => {
                    alert('Error loading request details');
                });
        }
        
        function cancelRequest(requestID, title) {
            document.getElementById('cancel_request_ID').value = requestID;
            document.getElementById('cancelMessage').textContent = 'Cancel request: "' + title + '"?';
            document.getElementById('cancelModal').style.display = 'block';
        }
        
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>
