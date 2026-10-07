<?php
require 'config.php';
require 'header.php';

$uid = $_SESSION['user_id'];

/* ADD MEMBER */
if (isset($_POST['add'])) {

    $name = trim($_POST['name']);
    $relation = trim($_POST['relation']);
    $age = $_POST['age'];
    $emergency_contact = trim($_POST['emergency_contact']);

    $stmt = $conn->prepare(
        "INSERT INTO family_members
        (user_id, name, relation, age, emergency_contact)
        VALUES (?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "issis",
        $uid,
        $name,
        $relation,
        $age,
        $emergency_contact
    );

    $stmt->execute();

    header("Location: members.php");
    exit;
}


/* DELETE MEMBER */
if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    $stmt = $conn->prepare(
        "DELETE FROM family_members
         WHERE id = ? AND user_id = ?"
    );

    $stmt->bind_param("ii", $id, $uid);
    $stmt->execute();

    header("Location: members.php");
    exit;
}


/* UPDATE MEMBER */
if (isset($_POST['update'])) {

    $id = (int)$_POST['id'];

    $name = trim($_POST['name']);
    $relation = trim($_POST['relation']);
    $age = $_POST['age'];
    $emergency_contact = trim($_POST['emergency_contact']);

    $stmt = $conn->prepare(
        "UPDATE family_members
         SET name = ?, relation = ?, age = ?, emergency_contact = ?
         WHERE id = ? AND user_id = ?"
    );

    $stmt->bind_param(
        "ssisii",
        $name,
        $relation,
        $age,
        $emergency_contact,
        $id,
        $uid
    );

    $stmt->execute();

    header("Location: members.php");
    exit;
}


/* GET MEMBER FOR EDIT */
$edit_member = null;

if (isset($_GET['edit'])) {

    $id = (int)$_GET['edit'];

    $stmt = $conn->prepare(
        "SELECT * FROM family_members
         WHERE id = ? AND user_id = ?"
    );

    $stmt->bind_param("ii", $id, $uid);
    $stmt->execute();

    $result = $stmt->get_result();

    $edit_member = $result->fetch_assoc();
}


/* GET ALL MEMBERS */
$list = $conn->query(
    "SELECT * FROM family_members
     WHERE user_id = $uid
     ORDER BY id DESC"
);
?>

<div class="container">

    <h2>👨‍👩‍👧 Family Members</h2>


    <?php if ($edit_member): ?>

        <!-- EDIT FORM -->

        <div class="form-card">

            <h3>✏️ Edit Family Member</h3>

            <form method="post">

                <input
                    type="hidden"
                    name="id"
                    value="<?= $edit_member['id'] ?>"
                >

                <label>Name</label>

                <input
                    type="text"
                    name="name"
                    value="<?= htmlspecialchars($edit_member['name']) ?>"
                    required
                >


                <label>Relation</label>

                <input
                    type="text"
                    name="relation"
                    value="<?= htmlspecialchars($edit_member['relation']) ?>"
                    required
                >


                <label>Age</label>

                <input
                    type="number"
                    name="age"
                    value="<?= htmlspecialchars($edit_member['age']) ?>"
                    min="0"
                >


                <label>Emergency Contact</label>

                <input
                    type="text"
                    name="emergency_contact"
                    value="<?= htmlspecialchars($edit_member['emergency_contact']) ?>"
                >


                <button
                    type="submit"
                    name="update"
                    class="btn"
                >
                    Update Member
                </button>

                <a
                    href="members.php"
                    class="btn secondary"
                >
                    Cancel
                </a>

            </form>

        </div>


    <?php else: ?>

        <!-- ADD FORM -->

        <div class="form-card">

            <h3>➕ Add Family Member</h3>

            <form method="post">

                <label>Name</label>

                <input
                    type="text"
                    name="name"
                    placeholder="Enter name"
                    required
                >


                <label>Relation</label>

                <input
                    type="text"
                    name="relation"
                    placeholder="Mother, Father, Brother..."
                    required
                >


                <label>Age</label>

                <input
                    type="number"
                    name="age"
                    min="0"
                >


                <label>Emergency Contact</label>

                <input
                    type="text"
                    name="emergency_contact"
                    placeholder="Enter contact number"
                >


                <button
                    type="submit"
                    name="add"
                    class="btn"
                >
                    Add Member
                </button>

            </form>

        </div>

    <?php endif; ?>


    <!-- MEMBER LIST -->

    <div class="card">

        <h3>Family Members List</h3>

        <table>

            <tr>

                <th>Name</th>

                <th>Relation</th>

                <th>Age</th>

                <th>Emergency Contact</th>

                <th>Action</th>

            </tr>


            <?php while ($row = $list->fetch_assoc()): ?>

                <tr>

                    <td>
                        <?= htmlspecialchars($row['name']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['relation']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['age']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['emergency_contact']) ?>
                    </td>

                    <td>

                        <a
                            class="btn small"
                            href="members.php?edit=<?= $row['id'] ?>"
                        >
                            Edit
                        </a>


                        <a
                            class="btn danger small"
                            href="members.php?delete=<?= $row['id'] ?>"
                            onclick="return confirm('Delete this family member?')"
                        >
                            Delete
                        </a>

                    </td>

                </tr>

            <?php endwhile; ?>

        </table>

    </div>

</div>