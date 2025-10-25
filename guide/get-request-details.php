<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    exit('Unauthorized');
}

require_once "../php/CustomPackage.php";

$customPackage = new CustomPackage();
$request_ID = $_GET['id'] ?? 0;

$request = $customPackage->getRequestById($request_ID);
if (!$request || $request['guide_ID'] != $_SESSION['user_id']) {
    exit('Request not found');
}

$messages = $customPackage->getRequestMessages($request_ID);
?>

<h2><?php echo htmlspecialchars($request['request_title']); ?></h2>

<div style="background: #f0f0f0; padding: 15px; border-radius: 5px; margin: 15px 0;">
    <p><strong>Status:</strong> <span class="badge badge-<?php echo $request['request_status']; ?>"><?php echo strtoupper($request['request_status']); ?></span></p>
    <p><strong>Tourist:</strong> <?php echo htmlspecialchars($request['tourist_name']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($request['tourist_email']); ?></p>
    <p><strong>Phone:</strong> <?php echo htmlspecialchars($request['tourist_phone']); ?></p>
</div>

<h3>Request Details</h3>
<div style="margin: 15px 0;">
    <p><strong>Description:</strong></p>
    <p><?php echo nl2br(htmlspecialchars($request['request_description'])); ?></p>
    
    <p><strong>Preferred Date:</strong> <?php echo $request['preferred_date'] ? date('F j, Y', strtotime($request['preferred_date'])) : 'Flexible'; ?></p>
    <p><strong>Duration:</strong> <?php echo htmlspecialchars($request['preferred_duration']); ?></p>
    <p><strong>Number of PAX:</strong> <?php echo $request['number_of_pax']; ?></p>
    <p><strong>Budget Range:</strong> <?php echo htmlspecialchars($request['budget_range']); ?></p>
    
    <?php if ($request['special_requirements']): ?>
        <p><strong>Special Requirements:</strong></p>
        <p><?php echo nl2br(htmlspecialchars($request['special_requirements'])); ?></p>
    <?php endif; ?>
    
    <?php if ($request['base_package_name']): ?>
        <p><strong>Based on Package:</strong> <?php echo htmlspecialchars($request['base_package_name']); ?></p>
    <?php endif; ?>
</div>

<?php if (!empty($request['spots'])): ?>
    <h3>Requested Tour Spots</h3>
    <ul class="spot-list">
        <?php foreach ($request['spots'] as $spot): ?>
            <?php 
                $priorityClass = '';
                $priorityLabel = '';
                if ($spot['priority'] == 1) {
                    $priorityClass = 'priority-high';
                    $priorityLabel = 'Must Visit';
                } elseif ($spot['priority'] == 2) {
                    $priorityClass = 'priority-medium';
                    $priorityLabel = 'Would Like to Visit';
                } else {
                    $priorityClass = 'priority-low';
                    $priorityLabel = 'Optional';
                }
            ?>
            <li class="spot-item <?php echo $priorityClass; ?>">
                <strong><?php echo htmlspecialchars($spot['spots_Name']); ?></strong> 
                <span style="font-size: 12px; color: #666;">(<?php echo $priorityLabel; ?>)</span>
                <br>
                <small><?php echo htmlspecialchars($spot['spots_Description']); ?></small>
                <?php if ($spot['notes']): ?>
                    <br><em>Note: <?php echo htmlspecialchars($spot['notes']); ?></em>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<h3>Messages</h3>
<div class="message-thread">
    <?php if (count($messages) > 0): ?>
        <?php foreach ($messages as $msg): ?>
            <div class="message <?php echo $msg['role_ID'] == 2 ? 'message-guide' : 'message-tourist'; ?>">
                <div class="message-sender"><?php echo htmlspecialchars($msg['sender_name']); ?></div>
                <div><?php echo nl2br(htmlspecialchars($msg['message_text'])); ?></div>
                <div class="message-time"><?php echo date('M j, Y g:i A', strtotime($msg['created_at'])); ?></div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align: center; color: #666;">No messages yet</p>
    <?php endif; ?>
</div>

<?php if ($request['request_status'] == 'pending' || $request['request_status'] == 'accepted'): ?>
    <form method="POST" action="package-requests.php" style="margin-top: 20px;">
        <input type="hidden" name="action" value="add_message">
        <input type="hidden" name="request_ID" value="<?php echo $request_ID; ?>">
        <div class="form-group">
            <label>Send Message to Tourist:</label>
            <textarea name="message_text" rows="3" required placeholder="Type your message here..."></textarea>
        </div>
        <button type="submit" class="btn btn-success">Send Message</button>
    </form>
<?php endif; ?>

<?php if ($request['request_status'] == 'rejected' && $request['rejection_reason']): ?>
    <div style="background: #f8d7da; padding: 15px; border-radius: 5px; margin: 15px 0;">
        <strong>Rejection Reason:</strong>
        <p><?php echo nl2br(htmlspecialchars($request['rejection_reason'])); ?></p>
    </div>
<?php endif; ?>
