<div class="container">
    <h2>Daftar Gallery Foto</h2>

    <!-- Display status message -->
    <?php if(!empty($success_msg)){ ?>
    <div class="col-xs-12">
        <div class="alert alert-success"><?php echo $success_msg; ?></div>
    </div>
    <?php }elseif(!empty($error_msg)){ ?>
    <div class="col-xs-12">
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    </div>
    <?php } ?>

    <div class="row">
        <div class="col-md-12 head">
            <h5><?php echo $title; ?></h5>
        </div>

        <!-- Data list table -->
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th width="5%">#</th>
                    <th width="20%"></th>
                    <th width="25%">Title</th>
                    <th width="15%">Diupload</th>
                    <th width="8%">Status</th>
                    <th width="18%">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($gallery)){ $i=0;
                    foreach($gallery as $row){ $i++;
                        $defaultImage = !empty($row['default_image'])?'<img src="'.base_url().'uploads/images/'.$row['default_image'].'" alt="" />':'';
                        $statusLink = ($row['status'] == 1)?site_url('manage_gallery/block/'.$row['id']):site_url('manage_gallery/unblock/'.$row['id']);
                        $statusTooltip = ($row['status'] == 1)?'Click to Inactive':'Click to Active';
                ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td class="thumbnail"><?php echo $defaultImage; ?></td>
                    <td><?php echo $row['title']; ?></td>
                    <td><?php echo $row['created']; ?></td>
                    <td><a href="<?php echo $statusLink; ?>" title="<?php echo $statusTooltip; ?>"><span class="badge <?php echo ($row['status'] == 1)?'badge-success':'badge-danger'; ?>"><?php echo ($row['status'] == 1)?'Active':'Inactive'; ?></span></a></td>
                    <td>
                        <a href="<?php echo base_url('manage_gallery/view/'.$row['id']); ?>" class="btn btn-primary">view</a>
                    </td>
                </tr>
                <?php } }else{ ?>
                <tr><td colspan="6">No gallery found...</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>