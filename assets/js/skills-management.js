document.addEventListener('DOMContentLoaded', function() {
    
    // 模态框控制
    const modal = document.getElementById('skill-modal');
    const addBtn = document.getElementById('add-skill-btn');
    const closeBtn = document.querySelector('.skill-modal-close');
    const cancelBtn = document.getElementById('cancel-skill');
    const saveBtn = document.getElementById('save-skill');
    const form = document.getElementById('skill-form');
    
    // 重置按钮（可能不存在）
    const resetBtn = document.getElementById('reset-skills-btn');
    
    // 打开添加技能模态框
    if (addBtn) {
        addBtn.addEventListener('click', function() {
            document.getElementById('modal-title').textContent = '添加技能';
            form.reset();
            document.getElementById('skill-id').value = '';
            document.getElementById('skill-color').value = '#3b82f6';
            modal.style.display = 'block';
        });
    }
    
    // 关闭模态框
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
    
    // 点击模态框外部关闭
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    }
    
    function closeModal() {
        if (modal) {
            modal.style.display = 'none';
            form.reset();
        }
    }
    
    // 编辑技能
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('edit-skill')) {
            const skillId = e.target.getAttribute('data-id');
            
            const formData = new FormData();
            formData.append('action', 'get_skill');
            formData.append('skill_id', skillId);
            formData.append('nonce', skillsAjax.nonce);
            
            fetch(skillsAjax.ajaxurl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(response => {
                if (response.success) {
                    const skill = response.data;
                    
                    document.getElementById('modal-title').textContent = '编辑技能';
                    document.getElementById('skill-id').value = skill.id;
                    document.getElementById('skill-unique-id').value = skill.skill_id;
                    document.getElementById('skill-name').value = skill.name;
                    document.getElementById('skill-description').value = skill.description;
                    document.getElementById('skill-icon').value = skill.icon;
                    document.getElementById('skill-color').value = skill.color;
                    document.getElementById('skill-category').value = skill.category;
                    document.getElementById('skill-level').value = skill.level;
                    document.getElementById('skill-years').value = skill.experience_years;
                    document.getElementById('skill-months').value = skill.experience_months;
                    document.getElementById('skill-projects').value = skill.projects;
                    document.getElementById('skill-sort').value = skill.sort_order;
                    document.getElementById('skill-status').value = skill.status;
                    
                    modal.style.display = 'block';
                } else {
                    alert('获取技能信息失败：' + response.data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('请求失败，请重试');
            });
        }
    });
    
    // 删除技能
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-skill')) {
            const skillId = e.target.getAttribute('data-id');
            const skillName = e.target.closest('tr').querySelector('td:nth-child(3)').textContent;
            
            if (!confirm('确定要删除技能 "' + skillName + '" 吗？此操作不可恢复。')) {
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'delete_skill');
            formData.append('skill_id', skillId);
            formData.append('nonce', skillsAjax.nonce);
            
            fetch(skillsAjax.ajaxurl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(response => {
                if (response.success) {
                    const row = document.querySelector('tr[data-skill-id="' + skillId + '"]');
                    if (row) {
                        row.style.transition = 'opacity 0.3s';
                        row.style.opacity = '0';
                        setTimeout(() => {
                            row.remove();
                        }, 300);
                    }
                    alert('删除成功');
                } else {
                    alert('删除失败：' + response.data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('请求失败，请重试');
            });
        }
    });
    
    // 重置技能数据（仅当按钮存在时）
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            if (!confirm('确定要重置所有技能数据吗？\n\n此操作将：\n1. 删除所有现有技能数据\n2. 重新导入默认的51个技能\n3. 此操作不可恢复\n\n请确认是否继续？')) {
                return;
            }
            
            // 二次确认
            if (!confirm('最后确认：真的要重置所有技能数据吗？')) {
                return;
            }
            
            resetBtn.disabled = true;
            resetBtn.textContent = '重置中...';
            
            const formData = new FormData();
            formData.append('action', 'reset_skills_data');
            formData.append('nonce', skillsAjax.nonce);
            
            fetch(skillsAjax.ajaxurl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(response => {
                if (response.success) {
                    alert('技能数据重置成功！页面将自动刷新。');
                    location.reload();
                } else {
                    alert('重置失败：' + response.data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('请求失败，请重试');
            })
            .finally(() => {
                resetBtn.disabled = false;
                resetBtn.textContent = '重置技能数据';
            });
        });
    }
    
    // 保存技能
    if (saveBtn) {
        saveBtn.addEventListener('click', function() {
            // 基本验证
            const skillUniqueId = document.getElementById('skill-unique-id');
            const skillName = document.getElementById('skill-name');
            const skillColor = document.getElementById('skill-color');
            
            if (!skillUniqueId.value.trim()) {
                alert('请输入技能ID');
                return;
            }
            
            if (!skillName.value.trim()) {
                alert('请输入技能名称');
                return;
            }
            
            // 验证技能ID格式
            const skillIdPattern = /^[a-zA-Z0-9_]+$/;
            if (!skillIdPattern.test(skillUniqueId.value)) {
                alert('技能ID只能包含字母、数字和下划线');
                return;
            }
            
            // 验证颜色格式
            const colorPattern = /^#[0-9A-Fa-f]{6}$/;
            if (!colorPattern.test(skillColor.value)) {
                alert('请输入有效的颜色值');
                return;
            }
            
            saveBtn.disabled = true;
            saveBtn.textContent = '保存中...';
            
            // 创建FormData对象
            const formData = new FormData(form);
            formData.append('action', 'save_skill');
            formData.append('nonce', skillsAjax.nonce);
            
            fetch(skillsAjax.ajaxurl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(response => {
                if (response.success) {
                    alert('保存成功');
                    closeModal();
                    location.reload(); // 简单刷新页面
                } else {
                    alert('保存失败：' + response.data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('请求失败，请重试');
            })
            .finally(() => {
                saveBtn.disabled = false;
                saveBtn.textContent = '保存';
            });
        });
    }
    
    // 技能ID输入时自动转换为小写并替换空格为下划线
    const skillUniqueIdInput = document.getElementById('skill-unique-id');
    if (skillUniqueIdInput) {
        skillUniqueIdInput.addEventListener('input', function() {
            let value = this.value;
            value = value.toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '');
            this.value = value;
        });
    }
    
    // 技能名称输入时自动生成技能ID（仅在添加时）
    const skillNameInput = document.getElementById('skill-name');
    const skillIdInput = document.getElementById('skill-id');
    if (skillNameInput && skillIdInput && skillUniqueIdInput) {
        skillNameInput.addEventListener('input', function() {
            if (skillIdInput.value === '') { // 只在添加新技能时自动生成
                let name = this.value;
                let id = name.toLowerCase()
                            .replace(/\s+/g, '_')
                            .replace(/[^a-z0-9_\u4e00-\u9fa5]/g, '')
                            .replace(/[\u4e00-\u9fa5]/g, function(match) {
                                // 简单的中文转拼音映射（可以扩展）
                                const pinyinMap = {
                                    '前端': 'frontend',
                                    '后端': 'backend',
                                    '数据库': 'database',
                                    '工具': 'tools',
                                    '其他': 'other'
                                };
                                return pinyinMap[match] || match;
                            });
                skillUniqueIdInput.value = id;
            }
        });
    }
    
    // 经验月数限制
    const skillMonthsInput = document.getElementById('skill-months');
    if (skillMonthsInput) {
        skillMonthsInput.addEventListener('input', function() {
            let value = parseInt(this.value);
            if (value > 11) {
                this.value = 11;
            }
            if (value < 0) {
                this.value = 0;
            }
        });
    }
    
    // 排序值验证
    const skillSortInput = document.getElementById('skill-sort');
    if (skillSortInput) {
        skillSortInput.addEventListener('input', function() {
            let value = parseInt(this.value);
            if (value < 0) {
                this.value = 0;
            }
        });
    }
    
    // 实时预览图标
    const skillIconInput = document.getElementById('skill-icon');
    if (skillIconInput) {
        skillIconInput.addEventListener('input', function() {
            const iconClass = this.value;
            let preview = this.nextElementSibling;
            
            if (!preview || !preview.classList.contains('icon-preview')) {
                preview = document.createElement('span');
                preview.className = 'icon-preview';
                preview.style.marginLeft = '10px';
                this.parentNode.insertBefore(preview, this.nextSibling);
            }
            
            if (iconClass) {
                const colorInput = document.getElementById('skill-color');
                const color = colorInput ? colorInput.value : '#3b82f6';
                preview.innerHTML = `<i class="${iconClass}" style="font-size: 20px; color: ${color};"></i>`;
            } else {
                preview.innerHTML = '';
            }
        });
    }
    
    // 颜色变化时更新图标预览
    const skillColorInput = document.getElementById('skill-color');
    if (skillColorInput) {
        skillColorInput.addEventListener('change', function() {
            const color = this.value;
            const previewIcon = document.querySelector('.icon-preview i');
            if (previewIcon) {
                previewIcon.style.color = color;
            }
        });
    }
    
    // 表格排序（简单的拖拽排序可以后续添加）
    
    // 键盘快捷键
    document.addEventListener('keydown', function(e) {
        // ESC键关闭模态框
        if (e.keyCode === 27 && modal && modal.style.display === 'block') {
            closeModal();
        }
        
        // Ctrl+S保存（在模态框打开时）
        if (e.ctrlKey && e.keyCode === 83 && modal && modal.style.display === 'block') {
            e.preventDefault();
            if (saveBtn) {
                saveBtn.click();
            }
        }
    });
    
    // 表单验证增强
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (saveBtn) {
                saveBtn.click();
            }
        });
    }
    
    // 初始化时的一些设置
    function initializeForm() {
        // 设置默认值
        const skillColor = document.getElementById('skill-color');
        const skillStatus = document.getElementById('skill-status');
        const skillSort = document.getElementById('skill-sort');
        const skillYears = document.getElementById('skill-years');
        const skillMonths = document.getElementById('skill-months');
        
        if (skillColor) skillColor.value = '#3b82f6';
        if (skillStatus) skillStatus.value = 'active';
        if (skillSort) skillSort.value = '0';
        if (skillYears) skillYears.value = '0';
        if (skillMonths) skillMonths.value = '0';
    }
    
    // 页面加载完成后的初始化
    initializeForm();
    
    // 添加一些用户体验增强
    const formElements = document.querySelectorAll('.form-group input, .form-group select, .form-group textarea');
    formElements.forEach(element => {
        element.addEventListener('focus', function() {
            const formGroup = this.closest('.form-group');
            if (formGroup) {
                formGroup.classList.add('focused');
            }
        });
        
        element.addEventListener('blur', function() {
            const formGroup = this.closest('.form-group');
            if (formGroup) {
                formGroup.classList.remove('focused');
            }
        });
    });
    
    // 添加加载状态
    function showLoading() {
        const loadingOverlay = document.createElement('div');
        loadingOverlay.id = 'loading-overlay';
        loadingOverlay.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999999; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;';
        loadingOverlay.textContent = '加载中...';
        document.body.appendChild(loadingOverlay);
    }
    
    function hideLoading() {
        const loadingOverlay = document.getElementById('loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.remove();
        }
    }
    
    // ==================== 批量操作功能 ====================
    
    // 全选/取消全选
    const selectAllSkills = document.getElementById('select-all-skills');
    if (selectAllSkills) {
        selectAllSkills.addEventListener('change', function() {
            const isChecked = this.checked;
            const skillCheckboxes = document.querySelectorAll('.skill-checkbox');
            skillCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            updateBatchActions();
            updateRowHighlight();
        });
    }
    
    // 单个复选框变化
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('skill-checkbox')) {
            updateSelectAllState();
            updateBatchActions();
            updateRowHighlight();
        }
    });

    // 更新全选状态
    function updateSelectAllState() {
        const totalCheckboxes = document.querySelectorAll('.skill-checkbox').length;
        const checkedCheckboxes = document.querySelectorAll('.skill-checkbox:checked').length;
        
        if (selectAllSkills) {
            if (checkedCheckboxes === 0) {
                selectAllSkills.indeterminate = false;
                selectAllSkills.checked = false;
            } else if (checkedCheckboxes === totalCheckboxes) {
                selectAllSkills.indeterminate = false;
                selectAllSkills.checked = true;
            } else {
                selectAllSkills.indeterminate = true;
                selectAllSkills.checked = false;
            }
        }
    }
    
    // 更新批量操作按钮显示状态
    function updateBatchActions() {
        const checkedCount = document.querySelectorAll('.skill-checkbox:checked').length;
        const selectedCountElement = document.getElementById('selected-count');
        if (selectedCountElement) {
            selectedCountElement.textContent = checkedCount;
        }
        
        // 显示或隐藏批量操作信息和按钮
        const batchInfo = document.querySelector('.batch-info');
        const batchButtons = document.querySelectorAll('.batch-buttons button');
        
        if (checkedCount > 0) {
            // 有选中项时显示信息和按钮
            if (batchInfo) {
                batchInfo.style.display = 'inline';
            }
            batchButtons.forEach(button => {
                button.style.display = 'inline-block';
            });
        } else {
            // 没有选中项时隐藏信息和按钮
            if (batchInfo) {
                batchInfo.style.display = 'none';
            }
            batchButtons.forEach(button => {
                button.style.display = 'none';
            });
        }
    }
    
    // 更新行高亮显示
    function updateRowHighlight() {
        const skillCheckboxes = document.querySelectorAll('.skill-checkbox');
        skillCheckboxes.forEach(checkbox => {
            const row = checkbox.closest('tr');
            if (checkbox.checked) {
                row.classList.add('selected');
            } else {
                row.classList.remove('selected');
            }
        });
    }
    
    // 批量启用按钮
    const batchEnableBtn = document.getElementById('batch-enable-btn');
    if (batchEnableBtn) {
        batchEnableBtn.addEventListener('click', function() {
            const selectedIds = getSelectedSkillIds();
            
            if (selectedIds.length === 0) {
                alert('请选择要操作的技能');
                return;
            }
            
            if (!confirm(`确定要启用选中的 ${selectedIds.length} 个技能吗？`)) {
                return;
            }
            
            batchToggleStatus(selectedIds, 'active');
        });
    }
    
    // 批量禁用按钮
    const batchDisableBtn = document.getElementById('batch-disable-btn');
    if (batchDisableBtn) {
        batchDisableBtn.addEventListener('click', function() {
            const selectedIds = getSelectedSkillIds();
            
            if (selectedIds.length === 0) {
                alert('请选择要操作的技能');
                return;
            }
            
            if (!confirm(`确定要禁用选中的 ${selectedIds.length} 个技能吗？`)) {
                return;
            }
            
            batchToggleStatus(selectedIds, 'inactive');
        });
    }
    
    // 批量删除按钮
    const batchDeleteBtn = document.getElementById('batch-delete-btn');
    if (batchDeleteBtn) {
        batchDeleteBtn.addEventListener('click', function() {
            const selectedIds = getSelectedSkillIds();
            
            if (selectedIds.length === 0) {
                alert('请选择要操作的技能');
                return;
            }
            
            if (!confirm(`确定要删除选中的 ${selectedIds.length} 个技能吗？\n\n此操作不可恢复！`)) {
                return;
            }
            
            // 二次确认
            if (!confirm('最后确认：真的要删除这些技能吗？')) {
                return;
            }
            
            batchDeleteSkills(selectedIds);
        });
    }
    
    // 获取选中的技能ID
    function getSelectedSkillIds() {
        const selectedIds = [];
        const checkedBoxes = document.querySelectorAll('.skill-checkbox:checked');
        checkedBoxes.forEach(checkbox => {
            selectedIds.push(checkbox.value);
        });
        return selectedIds;
    }
    
    // 批量切换状态
    function batchToggleStatus(skillIds, status) {
        const actionText = status === 'active' ? '启用' : '禁用';
        
        // 禁用所有批量操作按钮
        const batchButtons = document.querySelectorAll('.batch-buttons button');
        batchButtons.forEach(button => button.disabled = true);
        
        const formData = new FormData();
        formData.append('action', 'batch_toggle_status');
        // 将数组作为多个参数发送
        skillIds.forEach(function(id) {
            formData.append('skill_ids[]', id);
        });
        formData.append('status', status);
        formData.append('nonce', skillsAjax.nonce);
        
        fetch(skillsAjax.ajaxurl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.data);
                location.reload(); // 刷新页面显示最新状态
            } else {
                alert('操作失败：' + data.data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('请求失败，请重试');
        })
        .finally(() => {
            batchButtons.forEach(button => button.disabled = false);
        });
    }
    
    // 批量删除技能
    function batchDeleteSkills(skillIds) {
        // 禁用所有批量操作按钮
        const batchButtons = document.querySelectorAll('.batch-buttons button');
        batchButtons.forEach(button => button.disabled = true);
        
        const formData = new FormData();
        formData.append('action', 'batch_delete_skills');
        // 将数组作为多个参数发送
        skillIds.forEach(function(id) {
            formData.append('skill_ids[]', id);
        });
        formData.append('nonce', skillsAjax.nonce);
        
        fetch(skillsAjax.ajaxurl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.data);
                
                // 移除已删除的行
                let deletedCount = 0;
                const totalToDelete = skillIds.length;
                
                skillIds.forEach(function(id) {
                    const row = document.querySelector(`tr[data-skill-id="${id}"]`);
                    if (row) {
                        row.style.transition = 'opacity 0.3s';
                        row.style.opacity = '0';
                        setTimeout(() => {
                            row.remove();
                            deletedCount++;
                            
                            // 只在最后一个删除完成时更新状态
                            if (deletedCount === totalToDelete) {
                                updateBatchActions();
                                updateSelectAllState();
                                updateRowHighlight();
                            }
                        }, 300);
                    }
                });
            } else {
                alert('删除失败：' + data.data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('请求失败，请重试');
        })
        .finally(() => {
            batchButtons.forEach(button => button.disabled = false);
        });
    }
    
    // 键盘快捷键扩展
    document.addEventListener('keydown', function(e) {
        // Ctrl+A 全选（当焦点不在输入框时）
        if (e.ctrlKey && e.keyCode === 65 && !['INPUT', 'TEXTAREA'].includes(e.target.tagName)) {
            e.preventDefault();
            const selectAllCheckbox = document.getElementById('select-all-skills');
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.dispatchEvent(new Event('change'));
            }
        }
        
        // Delete键删除选中项
        if (e.keyCode === 46 && !['INPUT', 'TEXTAREA'].includes(e.target.tagName)) {
            const selectedIds = getSelectedSkillIds();
            if (selectedIds.length > 0) {
                if (confirm(`确定要删除选中的 ${selectedIds.length} 个技能吗？\n此操作不可恢复！`)) {
                    if (confirm('最后确认：真的要删除这些技能吗？')) {
                        batchDeleteSkills(selectedIds);
                    }
                }
            }
        }
    });
    
    // 初始化批量操作状态
    updateBatchActions();
    updateSelectAllState();
});
