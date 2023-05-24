<!-- Modal Edit -->
<!-- <div class="modal fade" id="modal-edit" tabindex="-1" aria-labelledby="modal-editLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modal-editLabel">Edit Menu</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<? //= base_url('menu/edit/' . $menu['id']); 
                            ?>" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="editMenu" name="editMenu" placeholder="Nama Menu" value="<?= set_value($menu['menu']) ?>">
                        <div class="form-text text-danger"><? //= form_error('menu/edit'); 
                                                            ?></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="edit" class="btn btn-primary">Edit</button>
                </div>
            </form>
        </div>
    </div>
</div> -->

<div class="container">
    <div class="row mt-3">
        <div class="col-6 m-auto">
            <div class="card">
                <div class="card-header">
                    Edit Menu
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="mb-3">
                            <input type="text" class="form-control" id="editMenu" name="editMenu" placeholder="Nama Menu" value="<?= $menu['menu']; ?>">
                            <div class="form-text text-danger"><?php echo form_error('menu/edit'); ?></div>
                        </div>
                        <button type="reset" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="edit" class="btn btn-primary">Edit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>