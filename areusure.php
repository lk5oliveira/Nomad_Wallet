<style>
    .delete-container {
        width: 100%;
        height: 100%;
        background-color: rgba(112, 112, 112, 0.5);
        height: 100%;
        width: 100%;
        position: fixed;
        z-index: 998;
        display: grid;
        place-items: center;
    }

    .delete-div {
        background-color: rgb(22 52 70);
        color: white;
        padding: 20px;
        border-radius: 20px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        width: fit-content;
        height: fit-content;
    }

    .delete-div h2.title {
        color: #fdfcdc;
        letter-spacing: 0px;
    }

    .buttons, .buttons form {
    display: flex;
    width: 60%;
    justify-content: space-evenly;
    margin-top: 10px;
    }

    .buttons form {
        width: 100%;
        gap: 1.5rem;
    }

    .buttons > form button {
        width: 50%;
        border-radius: 10px;
        font-weight: 700;
        color: #06113C;
        border: 2px solid #06113C;
        transition: 0.2s;
        height: 3em;
        border: none;
        color: white;
    }

    button:hover, button:focus {
        opacity: 1.2;
        color: white;
        border: none;
        box-shadow: 5px 5px 15px 5px rgba(0,0,0,0.1);
        cursor: pointer;
    }

    .delete-button {
        background-color: #f07167;
    }

    .cancel {
        background-color: #98abcd;
    }

</style>

<div class="delete-container" id="delete-container" style="display: none;">
    <div class="delete-div" id="delete-div">
        <h2 class='title' id='title'>Delete?</h2>
        <p>Are you sure you want to delete this transaction?</p>
        <div class="buttons">
            <form action="/include/delete.php" method="post">
                <input type="hidden" name="id" id="id-input-delete" require>
                <?php if ($_SERVER['REQUEST_URI'] != '/history.php') : ?>
                    <input type="hidden" name="all" id="all-input" value="all" disabled required>
                <?php endif ?>
                <button class="delete-button" id="delete-button" type="submit" name="update">delete</button>
                <button class="cancel" type="button" onclick="closeDeleteAlert();">cancel</button>
            </form>
        </div>      
    </div>
</div>

<script>
    
    <?php if(isset($_GET['edit'])) : ?>
    function getTransactionId() {
    /* Insert the transaction ID on the hidden ID input fiel */
    let inputId = document.getElementById('id-input-delete');
    inputId.value = '<?= $_GET['edit']; ?>';
    }
    <?php endif ?>

    let deleteContainer = document.getElementById('delete-container');

    function openDeleteAlert() {
        deleteContainer.style.display = 'grid';
    }

    function closeDeleteAlert() {
        deleteContainer.style.display = 'none';
    }

    function disableInput() {
    let allInput = document.getElementById('all-input').disabled = true;
    }

    function enableInput() {
    let allInput = document.getElementById('all-input').disabled = false;
    }

</script>