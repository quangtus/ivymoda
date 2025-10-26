<?php
// filepath: c:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\shared\admin\footer.php
?>
        </div>
    </div>
</section>

<script src="<?php echo BASE_URL; ?>assets/js/admin-script.js"></script>
    <!-- Custom Scripts -->
    <script>
        $(document).ready(function() {
            let imageGroupIndex = 1;
            
            $('#add-image-group').click(function() {
                const newGroup = `
                    <div class="image-upload-group" data-group-index="${imageGroupIndex}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Chọn màu cho nhóm ảnh:</label>
                                    <select name="image_color_groups[${imageGroupIndex}]" class="form-control">
                                        <option value="">-- Không chọn màu --</option>
                                        <?php foreach($colors as $color): ?>
                                            <option value="<?= $color->color_id ?>">
                                                <?= htmlspecialchars($color->color_ten) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Chọn ảnh:</label>
                                    <input type="file" 
                                           name="product_images[${imageGroupIndex}][]" 
                                           class="form-control-file" 
                                           multiple 
                                           accept="image/jpeg,image/png,image/gif,image/webp">
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-danger btn-sm remove-image-group mt-2">
                            <i class="fas fa-trash"></i> Xóa nhóm
                        </button>
                    </div>
                `;
                
                $('#image-upload-container').append(newGroup);
                imageGroupIndex++;
            });

            // Xóa nhóm ảnh
            $(document).on('click', '.remove-image-group', function() {
                $(this).closest('.image-upload-group').remove();
            });
        });
    </script>
</body>
</html>