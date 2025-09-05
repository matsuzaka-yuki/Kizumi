<?php
/**
 * 技能展示页面相关函数
 * 基于mizuki主题设计的技能数据管理
 */

// 防止直接访问
if (!defined('ABSPATH')) {
    exit;
}

/**
 * 获取技能数据
 * @return array 技能数据数组
 */
function get_skills_data() {
    // 可以从数据库、配置文件或直接定义获取数据
    // 这里提供一个示例数据结构
    return [
        // 前端技能
        [
            'id' => 'javascript',
            'name' => 'JavaScript',
            'description' => '现代JavaScript开发，包括ES6+语法、异步编程、模块化开发等。',
            'icon' => 'fab fa-js-square',
            'category' => 'frontend',
            'level' => 'advanced',
            'experience' => ['years' => 3, 'months' => 6],
            'projects' => ['博客系统', '数据可视化工具', '作品集网站'],
            'color' => '#F7DF1E'
        ],
        [
            'id' => 'typescript',
            'name' => 'TypeScript',
            'description' => '类型安全的JavaScript超集，提升代码质量和开发效率。',
            'icon' => 'fab fa-js-square',
            'category' => 'frontend',
            'level' => 'advanced',
            'experience' => ['years' => 2, 'months' => 8],
            'projects' => ['博客系统', '任务管理应用'],
            'color' => '#3178C6'
        ],
        [
            'id' => 'react',
            'name' => 'React',
            'description' => '构建用户界面的JavaScript库，包括Hooks、Context、状态管理等。',
            'icon' => 'fab fa-react',
            'category' => 'frontend',
            'level' => 'advanced',
            'experience' => ['years' => 2, 'months' => 10],
            'projects' => ['作品集网站', '任务管理应用'],
            'color' => '#61DAFB'
        ],
        [
            'id' => 'vue',
            'name' => 'Vue.js',
            'description' => '渐进式JavaScript框架，易学易用，适合快速开发。',
            'icon' => 'fab fa-vuejs',
            'category' => 'frontend',
            'level' => 'intermediate',
            'experience' => ['years' => 1, 'months' => 8],
            'projects' => ['数据可视化工具'],
            'color' => '#4FC08D'
        ],
        [
            'id' => 'html5',
            'name' => 'HTML5',
            'description' => '现代Web标准，语义化标签、多媒体支持、离线应用等。',
            'icon' => 'fab fa-html5',
            'category' => 'frontend',
            'level' => 'expert',
            'experience' => ['years' => 4, 'months' => 0],
            'projects' => ['所有Web项目'],
            'color' => '#E34F26'
        ],
        [
            'id' => 'css3',
            'name' => 'CSS3',
            'description' => '现代CSS技术，包括Flexbox、Grid、动画、响应式设计等。',
            'icon' => 'fab fa-css3-alt',
            'category' => 'frontend',
            'level' => 'expert',
            'experience' => ['years' => 4, 'months' => 0],
            'projects' => ['所有Web项目'],
            'color' => '#1572B6'
        ],
        [
            'id' => 'sass',
            'name' => 'Sass/SCSS',
            'description' => 'CSS预处理器，提供变量、嵌套、混合等高级功能。',
            'icon' => 'fab fa-sass',
            'category' => 'frontend',
            'level' => 'intermediate',
            'experience' => ['years' => 2, 'months' => 3],
            'projects' => ['传统网站', '组件库'],
            'color' => '#CF649A'
        ],
        [
            'id' => 'bootstrap',
            'name' => 'Bootstrap',
            'description' => '流行的CSS框架，快速构建响应式网站。',
            'icon' => 'fab fa-bootstrap',
            'category' => 'frontend',
            'level' => 'advanced',
            'experience' => ['years' => 3, 'months' => 0],
            'projects' => ['企业网站', '管理后台'],
            'color' => '#7952B3'
        ],

        // 后端技能
        [
            'id' => 'php',
            'name' => 'PHP',
            'description' => '广泛使用的服务器端脚本语言，特别适合Web开发。',
            'icon' => 'fab fa-php',
            'category' => 'backend',
            'level' => 'advanced',
            'experience' => ['years' => 3, 'months' => 6],
            'projects' => ['WordPress主题', '电商系统', 'CMS系统'],
            'color' => '#777BB4'
        ],
        [
            'id' => 'nodejs',
            'name' => 'Node.js',
            'description' => '基于Chrome V8引擎的JavaScript运行时，用于服务端开发。',
            'icon' => 'fab fa-node-js',
            'category' => 'backend',
            'level' => 'intermediate',
            'experience' => ['years' => 2, 'months' => 3],
            'projects' => ['API服务', '实时聊天应用'],
            'color' => '#339933'
        ],
        [
            'id' => 'python',
            'name' => 'Python',
            'description' => '通用编程语言，适用于Web开发、数据分析、机器学习等。',
            'icon' => 'fab fa-python',
            'category' => 'backend',
            'level' => 'intermediate',
            'experience' => ['years' => 1, 'months' => 10],
            'projects' => ['数据分析工具', 'Web爬虫'],
            'color' => '#3776AB'
        ],
        [
            'id' => 'java',
            'name' => 'Java',
            'description' => '企业级应用开发的主流编程语言，跨平台、面向对象。',
            'icon' => 'fab fa-java',
            'category' => 'backend',
            'level' => 'intermediate',
            'experience' => ['years' => 2, 'months' => 0],
            'projects' => ['企业系统', '微服务API'],
            'color' => '#ED8B00'
        ],

        // 数据库技能
        [
            'id' => 'mysql',
            'name' => 'MySQL',
            'description' => '世界上最流行的开源关系型数据库管理系统。',
            'icon' => 'fas fa-database',
            'category' => 'database',
            'level' => 'advanced',
            'experience' => ['years' => 3, 'months' => 0],
            'projects' => ['电商平台', '博客系统', '用户管理系统'],
            'color' => '#4479A1'
        ],
        [
            'id' => 'postgresql',
            'name' => 'PostgreSQL',
            'description' => '强大的开源关系型数据库管理系统。',
            'icon' => 'fas fa-database',
            'category' => 'database',
            'level' => 'intermediate',
            'experience' => ['years' => 1, 'months' => 5],
            'projects' => ['数据分析平台'],
            'color' => '#336791'
        ],
        [
            'id' => 'mongodb',
            'name' => 'MongoDB',
            'description' => '面向文档的NoSQL数据库，灵活的数据模型。',
            'icon' => 'fas fa-leaf',
            'category' => 'database',
            'level' => 'intermediate',
            'experience' => ['years' => 1, 'months' => 2],
            'projects' => ['内容管理系统', 'API服务'],
            'color' => '#47A248'
        ],
        [
            'id' => 'redis',
            'name' => 'Redis',
            'description' => '高性能的内存数据结构存储，用作数据库、缓存和消息代理。',
            'icon' => 'fas fa-memory',
            'category' => 'database',
            'level' => 'intermediate',
            'experience' => ['years' => 1, 'months' => 3],
            'projects' => ['缓存系统', '会话存储'],
            'color' => '#DC382D'
        ],

        // 工具技能
        [
            'id' => 'git',
            'name' => 'Git',
            'description' => '分布式版本控制系统，代码管理和团队协作必备工具。',
            'icon' => 'fab fa-git-alt',
            'category' => 'tools',
            'level' => 'advanced',
            'experience' => ['years' => 3, 'months' => 6],
            'projects' => ['所有项目'],
            'color' => '#F05032'
        ],
        [
            'id' => 'docker',
            'name' => 'Docker',
            'description' => '容器化平台，简化应用部署和环境管理。',
            'icon' => 'fab fa-docker',
            'category' => 'tools',
            'level' => 'intermediate',
            'experience' => ['years' => 1, 'months' => 8],
            'projects' => ['微服务部署', '开发环境'],
            'color' => '#2496ED'
        ],
        [
            'id' => 'linux',
            'name' => 'Linux',
            'description' => '开源操作系统，服务器部署和开发环境的首选。',
            'icon' => 'fab fa-linux',
            'category' => 'tools',
            'level' => 'intermediate',
            'experience' => ['years' => 2, 'months' => 6],
            'projects' => ['服务器管理', '脚本编写'],
            'color' => '#FCC624'
        ],
        [
            'id' => 'vscode',
            'name' => 'VS Code',
            'description' => '轻量级但功能强大的代码编辑器，丰富的插件生态。',
            'icon' => 'fas fa-code',
            'category' => 'tools',
            'level' => 'expert',
            'experience' => ['years' => 4, 'months' => 0],
            'projects' => ['所有开发项目'],
            'color' => '#007ACC'
        ],
        [
            'id' => 'photoshop',
            'name' => 'Photoshop',
            'description' => '专业的图像编辑和设计软件。',
            'icon' => 'fas fa-paint-brush',
            'category' => 'tools',
            'level' => 'intermediate',
            'experience' => ['years' => 2, 'months' => 0],
            'projects' => ['UI设计', '图像处理'],
            'color' => '#31A8FF'
        ],

        // 其他技能
        [
            'id' => 'wordpress',
            'name' => 'WordPress',
            'description' => '世界上最流行的内容管理系统，主题和插件开发。',
            'icon' => 'fab fa-wordpress',
            'category' => 'other',
            'level' => 'expert',
            'experience' => ['years' => 3, 'months' => 6],
            'projects' => ['企业网站', '博客系统', '电商网站'],
            'certifications' => ['WordPress认证开发者'],
            'color' => '#21759B'
        ],
        [
            'id' => 'seo',
            'name' => 'SEO优化',
            'description' => '搜索引擎优化，提升网站在搜索结果中的排名。',
            'icon' => 'fas fa-search',
            'category' => 'other',
            'level' => 'intermediate',
            'experience' => ['years' => 2, 'months' => 0],
            'projects' => ['企业网站优化', '内容营销'],
            'color' => '#4285F4'
        ],
        [
            'id' => 'api-design',
            'name' => 'API设计',
            'description' => 'RESTful API设计与开发，接口文档编写。',
            'icon' => 'fas fa-plug',
            'category' => 'other',
            'level' => 'intermediate',
            'experience' => ['years' => 2, 'months' => 3],
            'projects' => ['微服务架构', '第三方集成'],
            'color' => '#FF6B6B'
        ],
        [
            'id' => 'responsive-design',
            'name' => '响应式设计',
            'description' => '多设备适配的网页设计，确保在各种屏幕尺寸下的良好体验。',
            'icon' => 'fas fa-mobile-alt',
            'category' => 'other',
            'level' => 'advanced',
            'experience' => ['years' => 3, 'months' => 0],
            'projects' => ['所有Web项目'],
            'color' => '#9C27B0'
        ]
    ];
}

/**
 * 获取技能统计信息
 * @return array 统计数据
 */
function get_skills_stats() {
    $skills = get_skills_data();
    $total = count($skills);
    
    $by_level = [
        'beginner' => 0,
        'intermediate' => 0,
        'advanced' => 0,
        'expert' => 0
    ];
    
    $by_category = [
        'frontend' => 0,
        'backend' => 0,
        'database' => 0,
        'tools' => 0,
        'other' => 0
    ];
    
    foreach ($skills as $skill) {
        $by_level[$skill['level']]++;
        $by_category[$skill['category']]++;
    }
    
    return [
        'total' => $total,
        'by_level' => $by_level,
        'by_category' => $by_category
    ];
}

/**
 * 按分类获取技能
 * @param string $category 分类名称
 * @return array 技能数组
 */
function get_skills_by_category($category = '') {
    $skills = get_skills_data();
    
    if (empty($category) || $category === 'all') {
        return $skills;
    }
    
    return array_filter($skills, function($skill) use ($category) {
        return $skill['category'] === $category;
    });
}

/**
 * 获取高级技能
 * @return array 高级技能数组
 */
function get_advanced_skills() {
    $skills = get_skills_data();
    
    return array_filter($skills, function($skill) {
        return in_array($skill['level'], ['advanced', 'expert']);
    });
}

/**
 * 计算总经验年数
 * @return array 总经验
 */
function get_total_experience() {
    $skills = get_skills_data();
    $total_months = 0;
    
    foreach ($skills as $skill) {
        $total_months += $skill['experience']['years'] * 12 + $skill['experience']['months'];
    }
    
    return [
        'years' => floor($total_months / 12),
        'months' => $total_months % 12
    ];
}

/**
 * 获取技能等级文本
 * @param string $level 等级
 * @return string 等级文本
 */
function get_skill_level_text($level) {
    $levels = [
        'expert' => '专家级',
        'advanced' => '高级',
        'intermediate' => '中级',
        'beginner' => '初级'
    ];
    
    return isset($levels[$level]) ? $levels[$level] : '未知';
}

/**
 * 获取分类文本
 * @param string $category 分类
 * @return string 分类文本
 */
function get_skill_category_text($category) {
    $categories = [
        'frontend' => '前端开发',
        'backend' => '后端开发',
        'database' => '数据库',
        'tools' => '开发工具',
        'other' => '其他技能'
    ];
    
    return isset($categories[$category]) ? $categories[$category] : $category;
}

/**
 * 格式化经验时间
 * @param array $experience 经验数据
 * @return string 格式化的经验文本
 */
function format_skill_experience($experience) {
    $text = '';
    
    if ($experience['years'] > 0) {
        $text .= $experience['years'] . '年';
    }
    
    if ($experience['months'] > 0) {
        $text .= $experience['months'] . '个月';
    }
    
    return $text ?: '新手';
}

/**
 * 获取技能进度宽度
 * @param string $level 技能等级
 * @return string 宽度百分比
 */
function get_skill_progress_width($level) {
    $widths = [
        'expert' => '100%',
        'advanced' => '80%',
        'intermediate' => '60%',
        'beginner' => '40%'
    ];
    
    return isset($widths[$level]) ? $widths[$level] : '20%';
}

/**
 * 注册技能页面样式和脚本
 */
function enqueue_skills_page_assets() {
    if (is_page_template('page-skills.php')) {
        // 注册样式
        wp_enqueue_style(
            'skills-page-style',
            get_template_directory_uri() . '/assets/css/skills-page.css',
            [],
            '1.0.0'
        );
        
        // 注册脚本
        wp_enqueue_script(
            'skills-page-script',
            get_template_directory_uri() . '/assets/js/skills-page.js',
            [],
            '1.0.0',
            true
        );
        
        // FontAwesome已通过主题样式文件加载
    }
}
add_action('wp_enqueue_scripts', 'enqueue_skills_page_assets');

/**
 * 添加技能页面到WordPress管理后台
 */
function add_skills_page_to_admin() {
    add_theme_page(
        '技能管理',
        '技能管理',
        'manage_options',
        'skills-management',
        'skills_management_page'
    );
}
add_action('admin_menu', 'add_skills_page_to_admin');

/**
 * 技能管理页面内容
 */
function skills_management_page() {
    ?>
    <div class="wrap">
        <h1>技能管理</h1>
        <p>这里可以管理技能展示页面的数据。</p>
        
        <div class="card">
            <h2>当前技能统计</h2>
            <?php
            $stats = get_skills_stats();
            ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>类别</th>
                        <th>数量</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>总技能数</td>
                        <td><?php echo $stats['total']; ?></td>
                    </tr>
                    <tr>
                        <td>专家级</td>
                        <td><?php echo $stats['by_level']['expert']; ?></td>
                    </tr>
                    <tr>
                        <td>高级</td>
                        <td><?php echo $stats['by_level']['advanced']; ?></td>
                    </tr>
                    <tr>
                        <td>中级</td>
                        <td><?php echo $stats['by_level']['intermediate']; ?></td>
                    </tr>
                    <tr>
                        <td>初级</td>
                        <td><?php echo $stats['by_level']['beginner']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="card">
            <h2>分类统计</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>分类</th>
                        <th>数量</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats['by_category'] as $category => $count): ?>
                    <tr>
                        <td><?php echo get_skill_category_text($category); ?></td>
                        <td><?php echo $count; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="card">
            <h2>使用说明</h2>
            <ol>
                <li>创建一个新页面，选择"技能展示页面"模板</li>
                <li>在 <code>inc/skills-functions.php</code> 中的 <code>get_skills_data()</code> 函数中修改技能数据</li>
                <li>技能数据支持以下字段：
                    <ul>
                        <li><strong>id</strong>: 唯一标识符</li>
                        <li><strong>name</strong>: 技能名称</li>
                        <li><strong>description</strong>: 技能描述</li>
                        <li><strong>icon</strong>: Font Awesome图标类名</li>
                        <li><strong>category</strong>: 分类 (frontend/backend/database/tools/other)</li>
                        <li><strong>level</strong>: 等级 (beginner/intermediate/advanced/expert)</li>
                        <li><strong>experience</strong>: 经验 (years, months)</li>
                        <li><strong>projects</strong>: 相关项目数组 (可选)</li>
                        <li><strong>certifications</strong>: 认证数组 (可选)</li>
                        <li><strong>color</strong>: 主题色 (可选)</li>
                    </ul>
                </li>
            </ol>
        </div>
    </div>
    <?php
}

/**
 * 检查是否为技能页面模板
 * @param string $template 模板名称
 * @return bool
 */
function is_skills_page_template($template = '') {
    if (empty($template)) {
        $template = get_page_template_slug();
    }
    return $template === 'page-skills.php';
}

/**
 * 添加技能页面的body类
 * @param array $classes 现有类数组
 * @return array 修改后的类数组
 */
function add_skills_page_body_class($classes) {
    if (is_skills_page_template()) {
        $classes[] = 'skills-page';
        $classes[] = 'mizuki-style';
    }
    return $classes;
}
add_filter('body_class', 'add_skills_page_body_class');