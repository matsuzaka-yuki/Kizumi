<?php
/**
 * Template Name: 技能展示页面
 * Description: 基于mizuki主题设计的技能展示页面
 */

get_header(); 

// 技能数据现在从数据库获取，由技能管理系统提供
// get_skills_data() 函数在 fun-skills-management.php 中定义
?>

<div class="col-md-12 mx-auto">
    <div class="blog-single <?php echo kizumi_border_setting(); ?>">
        <?php while (have_posts()) : the_post(); ?>
        <div class="post-single">
            <!-- 页面头部信息 -->
            <div class="single-category">
                <span class="tag-cloud">
                    <i class="tagfa fa fa-code"></i>技能展示
                </span>
            </div>
            <h1 class="single-title"><?php the_title(); ?></h1>
            <hr class="horizontal dark">
            <div class="single-meta-box">
                <div class="single-info-left">
                    <div class="single-meta">
                        <img src="<?php echo kizumi_lazy_load_images(); ?>" data-src="<?php echo kizumi_get_avatar_url(get_the_author_meta('ID'), 100); ?>" class="avatar lazy" alt="avatar">
                        <div class="single-author-name">
                            <div class="single-author-info">
                                <a href="#" class="name">
                                    <i class="fa fa-at"></i><?php the_author(); ?>
                                </a>
                                <span class="data">
                                    <i class="fa fa-clock-o"></i><?php the_date(); ?>
                                </span>
                                <?php edit_post_link('<i class="fa fa-pencil-square-o"></i>编辑[' . __('仅作者可见', 'kizumi') . ']'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="single-info-right">
                    <div class="single-comnum">
                        <span class="tag-cloud">
                            <i class="fa fa-code"></i>技能展示页面
                        </span>
                    </div>
                </div>
            </div>

            <!-- 页面内容描述 -->
            <?php if (get_the_content()) : ?>
                <div class="single-content">
                    <?php the_content(); ?>
                </div>
            <?php endif; ?>

            <?php
            // 获取技能数据
            $skills_data = function_exists('get_skills_data') ? get_skills_data() : [];
            
            // 计算统计数据
            $stats = [
                'total' => count($skills_data),
                'by_level' => [
                    'expert' => count(array_filter($skills_data, function($s) { return isset($s['level']) && $s['level'] === 'expert'; })),
                    'advanced' => count(array_filter($skills_data, function($s) { return isset($s['level']) && $s['level'] === 'advanced'; })),
                    'intermediate' => count(array_filter($skills_data, function($s) { return isset($s['level']) && $s['level'] === 'intermediate'; })),
                    'beginner' => count(array_filter($skills_data, function($s) { return isset($s['level']) && $s['level'] === 'beginner'; }))
                ],
                'by_category' => [
                    'frontend' => count(array_filter($skills_data, function($s) { return isset($s['category']) && $s['category'] === 'frontend'; })),
                    'backend' => count(array_filter($skills_data, function($s) { return isset($s['category']) && $s['category'] === 'backend'; })),
                    'database' => count(array_filter($skills_data, function($s) { return isset($s['category']) && $s['category'] === 'database'; })),
                    'tools' => count(array_filter($skills_data, function($s) { return isset($s['category']) && $s['category'] === 'tools'; })),
                    'other' => count(array_filter($skills_data, function($s) { return isset($s['category']) && $s['category'] === 'other'; }))
                ]
            ];
            
            // 计算总经验
            $total_months = array_reduce($skills_data, function($total, $skill) {
                if (isset($skill['experience']) && is_array($skill['experience'])) {
                    $years = isset($skill['experience']['years']) ? (int)$skill['experience']['years'] : 0;
                    $months = isset($skill['experience']['months']) ? (int)$skill['experience']['months'] : 0;
                    return $total + $years * 12 + $months;
                }
                return $total;
            }, 0);
            $total_experience = floor($total_months / 12);
            
            // 技能分类
            $categories = [
                'frontend' => '前端开发',
                'backend' => '后端开发',
                'database' => '数据库',
                'tools' => '开发工具',
                'other' => '其他技能'
            ];
            ?>

          
<!-- 加载必要的CSS和JS -->
<!-- FontAwesome已通过主题样式文件加载 -->
<style>
/* CSS变量定义 - 支持夜间模式 */
:root {
    /* 浅色主题 */
    --bg-primary: #f8fafc;
    --bg-secondary: #ffffff;
    --bg-gradient-start: #3b82f6;
    --bg-gradient-end: #6366f1;
    --text-primary: #1e293b;
    --text-secondary: #64748b;
    --text-light: rgba(255, 255, 255, 0.9);
    --border-color: #e2e8f0;
    --border-light: #f1f5f9;
    --shadow-light: rgba(0, 0, 0, 0.1);
    --shadow-medium: rgba(0, 0, 0, 0.15);
    --shadow-dark: rgba(0, 0, 0, 0.3);
    
    /* 动态边框颜色 */
    --border-gradient-1: #3b82f6;
    --border-gradient-2: #6366f1;
    --border-gradient-3: #8b5cf6;
    --border-gradient-4: #ec4899;
}

/* 夜间模式 - 适配主题的夜间模式选择器 */
[data-bs-theme="dark"] {
    --bg-primary:rgb(196, 22, 22);
    --bg-secondary: #515a5a 
    --bg-gradient-start:rgb(177, 20, 20);
    --bg-gradient-end: #f8f8f8;
    --text-primary: #ffffff;
    --text-secondary: #ffffff;
    --text-light: rgba(255, 255, 255, 0.9);
    --border-color: #222222;
    --border-light: #333333;
    --shadow-light: rgba(0, 0, 0, 0.05);
    --shadow-medium: rgba(0, 0, 0, 0.1);
    --shadow-dark: rgba(0, 0, 0, 0.15);
}

/* 动态边框动画 */
@keyframes borderGlow {
    0% { 
        background: linear-gradient(45deg, var(--border-gradient-1), var(--border-gradient-2));
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
    }
    25% { 
        background: linear-gradient(45deg, var(--border-gradient-2), var(--border-gradient-3));
        box-shadow: 0 0 20px rgba(99, 102, 241, 0.3);
    }
    50% { 
        background: linear-gradient(45deg, var(--border-gradient-3), var(--border-gradient-4));
        box-shadow: 0 0 20px rgba(139, 92, 246, 0.3);
    }
    75% { 
        background: linear-gradient(45deg, var(--border-gradient-4), var(--border-gradient-1));
        box-shadow: 0 0 20px rgba(236, 72, 153, 0.3);
    }
    100% { 
        background: linear-gradient(45deg, var(--border-gradient-1), var(--border-gradient-2));
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
    }
}

@keyframes borderPulse {
    0%, 100% { 
        opacity: 1;
        transform: scale(1);
    }
    50% { 
        opacity: 0.7;
        transform: scale(1.02);
    }
}

/* 技能页面样式 - 适配主题布局 */
.skills-content {
    margin-top: 2rem;
}

/* 统计卡片样式 */
.skills-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 50px;
}

.stat-card {
    background: var(--bg-secondary, #fff);
    border-radius: 15px;
    padding: 30px;
    text-align: center;
    box-shadow: 0 10px 25px var(--shadow-light, rgba(0,0,0,0.1));
    border: 1px solid var(--border-color, #e9ecef);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #3b82f6, #6366f1);
    animation: borderGlow 3s ease-in-out infinite;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px var(--shadow-medium, rgba(0,0,0,0.15));
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: #3b82f6;
    margin-bottom: 10px;
    line-height: 1;
    transition: color 0.3s ease;
}

.stat-label {
    font-size: 0.875rem;
    color: var(--text-secondary, #6c757d);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    transition: color 0.3s ease;
}

/* 暗色主题适配 */
[data-bs-theme="dark"] .stat-card {
    background: #343a40;
    border-color: #495057;
    color: #f8f9fa;
}

[data-bs-theme="dark"] .stat-number {
    color: #0d6efd;
}

[data-bs-theme="dark"] .stat-label {
    color: #adb5bd;
}
=======

/* 技能部分 */
.skills-section, .skills-categories {
    margin-bottom: 50px;
}

.section-title, .category-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 30px;
    text-align: center;
    position: relative;
    transition: color 0.3s ease;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 3px;
    background: linear-gradient(90deg, var(--border-gradient-1), var(--border-gradient-2));
    border-radius: 2px;
}

.category-title {
    font-size: 1.5rem;
    text-align: left;
    margin-bottom: 20px;
}

.category-count {
    font-size: 1rem;
    font-weight: 400;
    color: var(--text-secondary);
    transition: color 0.3s ease;
}

/* 技能网格 */
.advanced-skills-grid, .skills-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.advanced-skills-grid {
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
}

.skill-card, .advanced-skill-card {
    background: var(--bg-secondary);
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 4px 15px var(--shadow-light);
    border: 1px solid var(--border-color);
    transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease, border-color 0.3s ease;
}

.skill-card:hover, .advanced-skill-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px var(--shadow-medium);
}

.skill-card-header, .advanced-skill-header {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    margin-bottom: 15px;
}

.skill-icon-container {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
    position: relative;
}

.skill-info {
    flex: 1;
    min-width: 0;
}

.skill-header-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
    gap: 15px;
}

.skill-name {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
    transition: color 0.3s ease;
}

.skill-level-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    flex-shrink: 0;
}

.skill-level-expert {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
}

.skill-level-advanced {
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
}

.skill-level-intermediate {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
}

.skill-level-beginner {
    background: rgba(59, 130, 246, 0.1);
    color: #2563eb;
}

.skill-description {
    color: var(--text-secondary);
    font-size: 0.875rem;
    line-height: 1.5;
    margin-bottom: 15px;
    transition: color 0.3s ease;
}

.skill-experience-section {
    margin-bottom: 15px;
}

.skill-experience-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.skill-experience-label {
    font-size: 0.75rem;
    color: var(--text-secondary);
    font-weight: 500;
    transition: color 0.3s ease;
}

.skill-experience-value {
    font-size: 0.75rem;
    color: var(--text-primary);
    font-weight: 600;
    transition: color 0.3s ease;
}

.skill-progress-bar {
    width: 100%;
    height: 8px;
    background: var(--border-light);
    border-radius: 10px;
    overflow: hidden;
    position: relative;
    transition: background-color 0.3s ease;
}

.skill-progress-fill {
    height: 100%;
    border-radius: inherit;
    transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

/* 分布图表 */
.skills-distribution {
    background: var(--bg-secondary);
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 10px 25px var(--shadow-light);
    border: 1px solid var(--border-color);
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.distribution-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 40px;
}

.distribution-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 20px;
    transition: color 0.3s ease;
}

.distribution-bars {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.distribution-bar {
    display: flex;
    align-items: center;
    gap: 12px;
}

.distribution-label {
    width: 80px;
    font-size: 0.875rem;
    color: var(--text-secondary);
    flex-shrink: 0;
    transition: color 0.3s ease;
}

.distribution-progress {
    flex: 1;
    height: 8px;
    background: var(--border-light);
    border-radius: 10px;
    overflow: hidden;
    transition: background-color 0.3s ease;
}

.distribution-fill {
    height: 100%;
    border-radius: inherit;
    transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
}

.distribution-count {
    width: 40px;
    text-align: right;
    font-size: 0.875rem;
    color: var(--text-primary);
    font-weight: 600;
    flex-shrink: 0;
    transition: color 0.3s ease;
}


</style>




            <?php
            // 获取高级技能
            $advanced_skills = array_filter($skills_data, function($skill) {
                return isset($skill['level']) && in_array($skill['level'], ['advanced', 'expert']);
            });

            // 按分类分组技能
            $skills_by_category = [
                'frontend' => array_filter($skills_data, function($s) { return isset($s['category']) && $s['category'] === 'frontend'; }),
                'backend' => array_filter($skills_data, function($s) { return isset($s['category']) && $s['category'] === 'backend'; }),
                'database' => array_filter($skills_data, function($s) { return isset($s['category']) && $s['category'] === 'database'; }),
                'tools' => array_filter($skills_data, function($s) { return isset($s['category']) && $s['category'] === 'tools'; }),
                'other' => array_filter($skills_data, function($s) { return isset($s['category']) && $s['category'] === 'other'; })
            ];

            // 辅助函数
            function get_level_text($level) {
                $levels = [
                    'expert' => '专家级',
                    'advanced' => '高级',
                    'intermediate' => '中级',
                    'beginner' => '初级'
                ];
                return $levels[$level] ?? '未知';
            }

            function get_level_width($level) {
                $widths = [
                    'expert' => '100%',
                    'advanced' => '80%',
                    'intermediate' => '60%',
                    'beginner' => '40%'
                ];
                return $widths[$level] ?? '20%';
            }

            function format_experience($experience) {
                if (!is_array($experience)) return '新手';
                $text = '';
                if (isset($experience['years']) && $experience['years'] > 0) {
                    $text .= $experience['years'] . '年';
                }
                if (isset($experience['months']) && $experience['months'] > 0) {
                    $text .= $experience['months'] . '个月';
                }
                return $text ?: '新手';
            }
            ?>

            <!-- 专业技能展示 -->
            <!-- 专业技能展示 -->
            <?php if (!empty($advanced_skills)): ?>
            <div class="skills-section">
                <h2 class="section-title">专业技能</h2>
                <div class="advanced-skills-grid">
                    <?php foreach ($advanced_skills as $skill): ?>
                    <div class="advanced-skill-card" style="--skill-color: <?php echo $skill['color'] ?? '#3b82f6'; ?>">
                        <div class="advanced-skill-header">
                            <div class="skill-icon-container" style="background-color: <?php echo ($skill['color'] ?? '#3b82f6') . '20'; ?>">
                                <i class="<?php echo $skill['icon']; ?>" style="color: <?php echo $skill['color'] ?? '#3b82f6'; ?>"></i>
                            </div>
                            <div class="skill-info">
                                <div class="skill-header-top">
                                    <h3 class="skill-name"><?php echo esc_html($skill['name']); ?></h3>
                                    <span class="skill-level-badge skill-level-<?php echo $skill['level']; ?>">
                                        <?php echo get_level_text($skill['level']); ?>
                                    </span>
                                </div>
                                <p class="skill-description"><?php echo esc_html($skill['description']); ?></p>
                                <div class="skill-experience-section">
                                    <div class="skill-experience-header">
                                        <span class="skill-experience-label">经验</span>
                                        <span class="skill-experience-value"><?php echo format_experience($skill['experience']); ?></span>
                                    </div>
                                    <div class="skill-progress-bar">
                                        <div class="skill-progress-fill" style="width: <?php echo get_level_width($skill['level']); ?>; background-color: <?php echo $skill['color'] ?? '#3b82f6'; ?>"></div>
                                    </div>
                                </div>
                                <?php if (!empty($skill['certifications'])): ?>
                                <div class="skill-certifications">
                                    <?php foreach ($skill['certifications'] as $cert): ?>
                                    <span class="certification-badge"><?php echo esc_html($cert); ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- 技能分类展示 -->
            <div class="skills-categories">
                <?php 
                $categories = [
                    'frontend' => '前端开发',
                    'backend' => '后端开发',
                    'database' => '数据库',
                    'tools' => '开发工具',
                    'other' => '其他技能'
                ];
                
                foreach ($categories as $category_key => $category_name): 
                    $category_skills = $skills_by_category[$category_key];
                    if (empty($category_skills)) continue;
                ?>
                <div class="category-section">
                    <h2 class="category-title">
                        <?php echo $category_name; ?>
                        <span class="category-count">(<?php echo count($category_skills); ?>)</span>
                    </h2>
                    <div class="skills-grid">
                        <?php foreach ($category_skills as $skill): ?>
                        <div class="skill-card" style="--skill-color: <?php echo $skill['color'] ?? '#3b82f6'; ?>">
                            <div class="skill-card-header">
                                <div class="skill-icon-container" style="background-color: <?php echo ($skill['color'] ?? '#3b82f6') . '20'; ?>">
                                    <i class="<?php echo $skill['icon']; ?>" style="color: <?php echo $skill['color'] ?? '#3b82f6'; ?>"></i>
                                </div>
                                <div class="skill-info">
                                    <div class="skill-header-top">
                                        <h3 class="skill-name"><?php echo esc_html($skill['name']); ?></h3>
                                        <span class="skill-level-badge skill-level-<?php echo $skill['level']; ?>">
                                            <?php echo get_level_text($skill['level']); ?>
                                        </span>
                                    </div>
                                    <p class="skill-description"><?php echo esc_html($skill['description']); ?></p>
                                    <div class="skill-experience-section">
                                        <div class="skill-experience-header">
                                            <span class="skill-experience-label">经验</span>
                                            <span class="skill-experience-value"><?php echo format_experience($skill['experience']); ?></span>
                                        </div>
                                        <div class="skill-progress-bar">
                                            <div class="skill-progress-fill" style="width: <?php echo get_level_width($skill['level']); ?>; background-color: <?php echo $skill['color'] ?? '#3b82f6'; ?>"></div>
                                        </div>
                                    </div>
                                    <?php if (!empty($skill['projects'])): ?>
                                    <div class="skill-projects-info">
                                        相关项目: <?php echo count($skill['projects']); ?> 个
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- 技能分布图表 -->
            <div class="skills-distribution">
                <h2 class="section-title">技能分布</h2>
                <div class="distribution-grid">
                    <!-- 按等级分布 -->
                    <div class="distribution-section">
                        <h3 class="distribution-title">按等级分布</h3>
                        <div class="distribution-bars">
                            <?php 
                            $levels = [
                                'expert' => ['label' => '专家级', 'color' => '#ef4444'],
                                'advanced' => ['label' => '高级', 'color' => '#f59e0b'],
                                'intermediate' => ['label' => '中级', 'color' => '#10b981'],
                                'beginner' => ['label' => '初级', 'color' => '#3b82f6']
                            ];
                            
                            foreach ($levels as $level_key => $level_info):
                                $count = $stats['by_level'][$level_key];
                                $percentage = $stats['total'] > 0 ? round(($count / $stats['total']) * 100) : 0;
                            ?>
                            <div class="distribution-bar">
                                <div class="distribution-label"><?php echo $level_info['label']; ?></div>
                                <div class="distribution-progress">
                                    <div class="distribution-fill" style="width: <?php echo $percentage; ?>%; background-color: <?php echo $level_info['color']; ?>"></div>
                                </div>
                                <div class="distribution-count"><?php echo $count; ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- 按分类分布 -->
                    <div class="distribution-section">
                        <h3 class="distribution-title">按分类分布</h3>
                        <div class="distribution-bars">
                            <?php 
                            $category_colors = [
                                'frontend' => '#3b82f6',
                                'backend' => '#10b981',
                                'database' => '#f59e0b',
                                'tools' => '#8b5cf6',
                                'other' => '#ef4444'
                            ];
                            
                            foreach ($categories as $category_key => $category_name):
                                $count = $stats['by_category'][$category_key];
                                if ($count === 0) continue;
                                $percentage = $stats['total'] > 0 ? round(($count / $stats['total']) * 100) : 0;
                            ?>
                            <div class="distribution-bar">
                                <div class="distribution-label"><?php echo $category_name; ?></div>
                                <div class="distribution-progress">
                                    <div class="distribution-fill" style="width: <?php echo $percentage; ?>%; background-color: <?php echo $category_colors[$category_key]; ?>"></div>
                                </div>
                                <div class="distribution-count"><?php echo $count; ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

<script>
// 简单的动画效果
document.addEventListener('DOMContentLoaded', function() {
    // 数字计数动画
    function animateCounter(element, target, duration = 2000) {
        let start = 0;
        const increment = target / (duration / 16);
        
        function animate() {
            start += increment;
            if (start < target) {
                element.textContent = Math.floor(start);
                requestAnimationFrame(animate);
            } else {
                element.textContent = target;
            }
        }
        animate();
    }
    
    // 为统计数字添加动画
    const statNumbers = document.querySelectorAll('.stat-number');
    statNumbers.forEach(function(element) {
        const target = parseInt(element.textContent);
        element.textContent = '0';
        setTimeout(function() {
            animateCounter(element, target);
        }, 500);
    });
    
    // 进度条动画
    const progressBars = document.querySelectorAll('.skill-progress-fill, .distribution-fill');
    progressBars.forEach(function(bar, index) {
        const width = bar.style.width;
        bar.style.width = '0';
        setTimeout(function() {
            bar.style.width = width;
        }, 1000 + index * 100);
    });
    
    // 卡片入场动画
    const cards = document.querySelectorAll('.stat-card, .skill-card, .advanced-skill-card');
    cards.forEach(function(card, index) {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        
        setTimeout(function() {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 200 + index * 100);
    });
});
</script>

        </div>
        <?php endwhile; ?>
    </div>
</div>

<?php get_footer(); ?>