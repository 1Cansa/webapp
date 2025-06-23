<?php
// $client is either null (creation) or the existing client object (editing)
// $linkedContacts available when editing a client
// $allContacts available for linking contacts
?>

<h1><?= $client ? 'Edit Client' : 'Create New Client'; ?></h1>

<form action="<?= $client ? '/clients/update/' . $client['id'] : '/clients/store'; ?>" method="POST">
    <div class="tabs">
        <button type="button" class="tab-button active" onclick="openTab(event, 'generalTab')">General</button>
        <?php if ($client): ?>
            <button type="button" class="tab-button" onclick="openTab(event, 'contactsTab')">Contact(s)</button>
        <?php endif; ?>
    </div>

    <div id="generalTab" class="tab-content active" style="display:block;">
        <h3>General Information</h3>
        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?= $client ? htmlspecialchars($client['name']) : ''; ?>" required>
        </div>
        <div>
            <label for="client_code">Client Code:</label>
            <input type="text" id="client_code" name="client_code" value="<?= $client ? htmlspecialchars($client['client_code']) : 'Auto-generated after saving'; ?>" readonly>
        </div>
        <button type="submit">Save Client</button>
    </div>

    <?php if ($client): ?>
    <div id="contactsTab" class="tab-content" style="display:none;">
        <h3>Linked Contacts</h3>
        <?php if (empty($linkedContacts)): ?>
            <p>No linked contacts.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($linkedContacts as $contact): ?>
                        <tr>
                            <td><?= htmlspecialchars($contact['last_name'] . ' ' . $contact['first_name']); ?></td>
                            <td><?= htmlspecialchars($contact['email']); ?></td>
                            <td>
                                <a href="?unlink_contact_id=<?= $contact['id']; ?>"
                                   onclick="return confirm('Are you sure you want to unlink this contact?');">Unlink</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <h3>Link a New Contact</h3>
        <?php if (empty($allContacts)): ?>
            <p>No available contacts to link. Please create contacts first.</p>
        <?php else: ?>
            <select name="link_contact_id">
                <option value="">Select a contact</option>
                <?php
                $linkedContactIds = array_column($linkedContacts, 'id');
                foreach ($allContacts as $contact):
                    if (!in_array($contact['id'], $linkedContactIds)):
                ?>
                    <option value="<?= $contact['id']; ?>"><?= htmlspecialchars($contact['last_name'] . ' ' . $contact['first_name'] . ' (' . $contact['email'] . ')'); ?></option>
                <?php
                    endif;
                endforeach;
                ?>
            </select>
            <button type="submit">Link Contact</button>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</form>
