<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends AUTH_Controller
{
    var $template = 'template/index';
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('upload');

        // Load gallery&admin model
        $this->load->model('gallery');
        $this->load->model('M_admin');
    }

    public function index()
    {
        $data['title'] = 'dashboard';
        $data['content'] = 'admin/dashboard';
        $data['userdata'] = $this->userdata;
        $this->load->view($this->template, $data);
    }

    public function List_upload()
    {
        $data = array();

        // Get messages from the session
        if ($this->session->userdata('success_msg')) {
            $data['success_msg'] = $this->session->userdata('success_msg');
            $this->session->unset_userdata('success_msg');
        }
        if ($this->session->userdata('error_msg')) {
            $data['error_msg'] = $this->session->userdata('error_msg');
            $this->session->unset_userdata('error_msg');
        }

        // Load the list page view
        $data['gallery'] = $this->gallery->getRows();
        $data['title'] = 'Gallery Archive';
        $data['userdata'] = $this->userdata;
        $data['content'] = 'admin/daftar_upload';
        $this->load->view($this->template, $data);
    }
    public function tampil($id)
    {
        $data = array();

        // Check whether id is not empty
        if (!empty($id)) {
            $data['gallery'] = $this->gallery->getRows($id);
            $data['title'] = $data['gallery']['title'];
            $data['content']     = 'admin/gallery_up';
            $data['userdata'] = $this->userdata;
            $this->load->view($this->template, $data);
        } else {
            return redirect('Admin/tampil');
        }
    }
    // upload gallery
    public function add()
    {
        $data = $galleryData = array();
        $errorUpload = '';

        // If add request is submitted
        if ($this->input->post('imgSubmit')) {
            $this->form_validation->set_rules('images', 'required');

            // Prepare gallery data
            $galleryData = array(
                'title' => $this->session->userdata('userdata')->role,
                'user_id' => $this->session->userdata('userdata')->id
            );

            // Validate submitted form data
            if (!$this->form_validation->run() == true) {
                // Insert gallery data
                $insert = $this->gallery->insert($galleryData);
                $galleryID = $insert;

                if ($insert) {
                    if (!empty($_FILES['images']['name'])) {
                        $filesCount = count($_FILES['images']['name']);
                        for ($i = 0; $i < $filesCount; $i++) {
                            $_FILES['file']['name']     = $_FILES['images']['name'][$i];
                            $_FILES['file']['type']     = $_FILES['images']['type'][$i];
                            $_FILES['file']['tmp_name'] = $_FILES['images']['tmp_name'][$i];
                            $_FILES['file']['error']    = $_FILES['images']['error'][$i];
                            $_FILES['file']['size']     = $_FILES['images']['size'][$i];

                            // File upload configuration
                            $uploadPath = 'uploads/images/';
                            $config['upload_path'] = $uploadPath;
                            $config['allowed_types'] = 'jpg|jpeg|png|gif';

                            // Load and initialize upload library
                            $this->load->library('upload', $config);
                            $this->upload->initialize($config);

                            // Upload file to server
                            if ($this->upload->do_upload('file')) {
                                // Uploaded file data
                                $fileData = $this->upload->data();
                                $uploadData[$i]['gallery_id'] = $galleryID;
                                $uploadData[$i]['file_name'] = $fileData['file_name'];
                                $uploadData[$i]['uploaded_on'] = format_indo(date('Y-m-d H:i:s'));
                            } else {

                                // $errorUpload .= $fileImages[$key] . '(' . $this->upload->display_errors('', '') . ') | ';
                            }
                        }

                        // File upload error message
                        $errorUpload = !empty($errorUpload) ? ' Upload Error: ' . trim($errorUpload, ' | ') : '';

                        if (!empty($uploadData)) {
                            // Insert files info into the database
                            $insert = $this->gallery->insertImage($uploadData);
                        }
                    }

                    $this->session->set_userdata('success_msg', 'Gallery Baru Berhasil Ditambahkan.' . $errorUpload);
                    return redirect('Admin/List_upload');
                } else {
                    $data['error_msg'] = 'Some problems occurred, please try again.';
                }
            }
        }

        $data['gallery'] = $galleryData;
        $data['title'] = 'Upload Gallery';
        $data['action'] = 'Add';

        // Load the add page view
        $data['content']     = 'admin/add-edit';
        $data['userdata'] = $this->userdata;
        $this->load->view($this->template, $data);
    }

    public function edit($id)
    {
        $data = $galleryData = array();

        // Get gallery data
        $galleryData = $this->gallery->getRows($id);

        // If update request is submitted
        if ($this->input->post('imgSubmit')) {
            // Form field validation rules
            $this->form_validation->set_rules('title', 'gallery title', 'required');

            // Prepare gallery data
            $galleryData = array(
                'title' => $this->input->post('title')
            );

            // Validate submitted form data
            if ($this->form_validation->run() == true) {
                // Update gallery data
                $update = $this->gallery->update($galleryData, $id);

                if ($update) {
                    if (!empty($_FILES['images']['name'])) {
                        $filesCount = count($_FILES['images']['name']);
                        for ($i = 0; $i < $filesCount; $i++) {
                            $_FILES['file']['name']     = $_FILES['images']['name'][$i];
                            $_FILES['file']['type']     = $_FILES['images']['type'][$i];
                            $_FILES['file']['tmp_name'] = $_FILES['images']['tmp_name'][$i];
                            $_FILES['file']['error']    = $_FILES['images']['error'][$i];
                            $_FILES['file']['size']     = $_FILES['images']['size'][$i];

                            // File upload configuration
                            $uploadPath = 'uploads/images/';
                            $config['upload_path'] = $uploadPath;
                            $config['allowed_types'] = 'jpg|jpeg|png|gif';

                            // Load and initialize upload library
                            $this->load->library('upload', $config);
                            $this->upload->initialize($config);

                            // Upload file to server
                            if ($this->upload->do_upload('file')) {
                                // Uploaded file data
                                $fileData = $this->upload->data();
                                $uploadData[$i]['gallery_id'] = $id;
                                $uploadData[$i]['file_name'] = $fileData['file_name'];
                                $uploadData[$i]['uploaded_on'] = date("Y-m-d H:i:s");
                            } else {
                                // $errorUpload .= $fileImages[$key] . '(' . $this->upload->display_errors('', '') . ') | ';
                            }
                        }

                        // File upload error message
                        $errorUpload = !empty($errorUpload) ? 'Upload Error: ' . trim($errorUpload, ' | ') : '';

                        if (!empty($uploadData)) {
                            // Insert files data into the database
                            $insert = $this->gallery->insertImage($uploadData);
                        }
                    }

                    $this->session->set_userdata('success_msg', 'Gallery Berhasil DI Ubah.' . $errorUpload);
                    return redirect('Admin/List_upload');
                } else {
                    $data['error_msg'] = 'Some problems occurred, please try again.';
                }
            }
        }


        $data['gallery'] = $galleryData;
        $data['title'] = 'Update Gallery';
        $data['action'] = 'Edit';
        $data['userdata'] = $this->userdata;

        // Load the add page view
        $data['content']     = 'admin/add-edit';
        $data['userdata'] = $this->userdata;
        $this->load->view($this->template, $data);
    }

    public function delete($id)
    {
        // Check whether id is not empty
        if ($id) {
            $galleryData = $this->gallery->getRows($id);

            // Delete gallery data
            $delete = $this->gallery->delete($id);

            if ($delete) {
                // Delete images data
                $condition = array('gallery_id' => $id);
                $deleteImg = $this->gallery->deleteImage($condition);

                // Remove files from the server
                if (!empty($galleryData['images'])) {
                    foreach ($galleryData['images'] as $img) {
                        @unlink('uploads/images/' . $img['file_name']);
                    }
                }

                $this->session->set_userdata('success_msg', 'Gallery berhasil di hapus.');
            } else {
                $this->session->set_userdata('error_msg', 'ada masalah mohon ulangi lagi.');
            }
        }

        return redirect('Admin/List_upload');
    }

    public function deleteImage()
    {
        $status  = 'err';
        // If post request is submitted via ajax
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
            $imgData = $this->gallery->getImgRow($id);

            // Delete image data
            $con = array('id' => $id);
            $delete = $this->gallery->deleteImage($con);

            if ($delete) {
                // Remove files from the server
                @unlink('uploads/images/' . $imgData['file_name']);
                $status = 'ok';
            }
        }
        echo $status;
        die;
    }

    public function block($id)
    {
        // Check whether gallery id is not empty
        if ($id) {
            // Update gallery status
            $data = array('status' => 0);
            $update = $this->gallery->update($data, $id);

            if ($update) {
                $this->session->set_userdata('success_msg', 'Gallery has been blocked successfully.');
            } else {
                $this->session->set_userdata('error_msg', 'Some problems occurred, please try again.');
            }
        }

        return redirect('Admin/List_upload');
    }

    public function unblock($id)
    {
        // Check whether gallery id is not empty
        if ($id) {
            // Update gallery status
            $data = array('status' => 1);
            $update = $this->gallery->update($data, $id);

            if ($update) {
                $this->session->set_userdata('success_msg', 'Gallery has been activated successfully.');
            } else {
                $this->session->set_userdata('error_msg', 'Some problems occurred, please try again.');
            }
        }

        return redirect('Admin/List_upload');
    }
}
