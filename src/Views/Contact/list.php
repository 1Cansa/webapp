
<?php
// $contacts is passed from the controller
?>

<h1>Contact List</h1>
<p><a href="/contacts/create">Create New Contact</a></p>

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
                    <td><?= htmlspecialchars($contact['first_name']); ?></td>
                    <td><?= htmlspecialchars($contact['last_name']); ?></td>
                    <td><?= htmlspecialchars($contact['email']); ?></td>
                    <td style="text-align:center;"><?= htmlspecialchars($contact['num_linked_clients']); ?></td>
                    <td>
                        <a href="<?= '/contacts/edit/' . $contact['id']; ?>">Edit</a> |
                        <a href="/contacts/delete/<?= $contact['id']; ?>" 
                            class="confirm-link" 
                            data-confirm="Are you sure you want to delete this contact? This action cannot be undone.">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
