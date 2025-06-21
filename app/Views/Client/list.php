<?php
// $clients is passed from the controller
?>

<h1>Client List</h1>
<p><a href="<?php echo BASE_URL; ?>/clients/create">Create New Client</a></p>

<?php if (empty($clients)): ?>
    <p>No clients found.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Client Code</th>
                <th style="text-align:center;">Number of Linked Contacts</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clients as $client): ?>
                <tr>
                    <td><?php echo htmlspecialchars($client['name']); ?></td>
                    <td><?php echo htmlspecialchars($client['client_code']); ?></td>
                    <td style="text-align:center;"><?php echo htmlspecialchars($client['num_linked_contacts']); ?></td>
                    <td>
                        <a href="<?php echo BASE_URL . '/clients/edit/' . $client['id']; ?>">Edit</a> |
                        <a href="<?php echo BASE_URL . '/clients/delete/' . $client['id']; ?>"
                           onclick="return confirm('Are you sure you want to delete this client and all its links?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
