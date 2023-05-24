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
                            <input type="text" class="form-control" id="title" name="title" placeholder="Title SubMenu" value="<?= $subMenu['title'] ?>">
                        </div>
                        <div class="mb-3">
                            <select name="menu_id" id="menu_id" class="form-control">
                                <option value="">Select Menu</option>
                                <?php foreach ($menu as $m) : ?>
                                    <!-- id di table user_menu -->
                                    <option value="<?= $m['id'] ?>"><?= $m['menu'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="url" name="url" placeholder="URL" value="<?= $subMenu['url'] ?>">
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="icon" name="icon" placeholder="icon" value="<?= $subMenu['icon'] ?>">
                        </div>
                        <div class="mb-3">
                            <!-- <input type="text" class="form-control" id="is_active" name="is_active" placeholder="Status"> -->
                            <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
                                <input type="checkbox" class="btn-check" id="is_active" name="is_active" value="1" autocomplete="off" checked>
                                <label class="btn btn-outline-primary" for="is_active">Active?</label>
                            </div>
                        </div>

                        <div class="">
                            <button type="reset" class="btn btn-secondary">Close</button>
                            <button type="submit" class="btn btn-primary">Edit Sub Menu</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>