<?php
// $contact is either null (for creation) or the contact object (for editing)
// $linkedClients (for editing)
// $allClients (for linking)
?>

<h1><?php echo $contact ? 'Edit Contact' : 'Create New Contact'; ?></h1>

<form action="<?= $contact ? '/Contacts/update/' . $contact['id'] : '/Contacts/store'; ?>" method="POST">
    <div class="tabs">
        <button type="button" class="tab-button active" onclick="openTab(event, 'generalTab')">General</button>
        <?php if ($contact): ?>
            <button type="button" class="tab-button" onclick="openTab(event, 'clientsTab')">Client(s)</button>
        <?php endif; ?>
    </div>

    <div id="generalTab" class="tab-content active" style="display:block;">
        <h3>General Information</h3>
        <div>
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo $contact ? htmlspecialchars($contact['first_name']) : ''; ?>" required>
        </div>
        <div>
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo $contact ? htmlspecialchars($contact['last_name']) : ''; ?>" required>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $contact ? htmlspecialchars($contact['email']) : ''; ?>" required>
        </div>
        <button type="submit">Save Contact</button>
    </div>

    <?php if ($contact): ?>
    <div id="clientsTab" class="tab-content" style="display:none;">
        <h3>Linked Clients</h3>
        <?php if (empty($linkedClients)): ?>
            <p>No linked clients.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Client Name</th>
                        <th>Client Code</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($linkedClients as $client): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($client['name']); ?></td>
                            <td><?php echo htmlspecialchars($client['client_code']); ?></td>
                            <td>
                                <a href="?unlink_client_id=<?php echo $client['id']; ?>"
                                   onclick="return confirm('Are you sure you want to unlink this client?');">Unlink</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <h3>Link a New Client</h3>
        <?php if (empty($allClients)): ?>
            <p>No available clients to link. Please create clients first.</p>
        <?php else: ?>
            <select name="link_client_id">
                <option value="">Select a client</option>
                <?php
                $linkedClientIds = array_column($linkedClients, 'id');
                foreach ($allClients as $client):
                    if (!in_array($client['id'], $linkedClientIds)):
                ?>
                    <option value="<?php echo $client['id']; ?>"><?php echo htmlspecialchars($client['name'] . ' (' . $client['client_code'] . ')'); ?></option>
                <?php
                    endif;
                endforeach;
                ?>
            </select>
            <button type="submit">Link Client</button>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</form>
