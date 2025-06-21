<?php
// $contacts is passed from the controller
?>

<h1>Contact List</h1>
<p><a href="<?php echo BASE_URL; ?>/contacts/create">Create New Contact</a></p>

<?php if (empty($contacts)): ?>
    <p>No contacts found.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th style="text-align:center;">Number of Linked Clients</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($contacts as $contact): ?>
                <tr>
                    <td><?php echo htmlspecialchars($contact['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($contact['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($contact['email']); ?></td>
                    <td style="text-align:center;"><?php echo htmlspecialchars($contact['num_linked_clients']); ?></td>
                    <td>
                        <a href="<?php echo BASE_URL . '/contacts/edit/' . $contact['id']; ?>">Edit</a> |
                        <a href="<?php echo BASE_URL . '/contacts/delete/' . $contact['id']; ?>" onclick="return confirm('Are you sure you want to delete this contact and all its links?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
