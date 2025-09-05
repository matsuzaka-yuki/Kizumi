<?php
/**
 * 技能管理系统
 * @package Kizumi
 */

// 防止直接访问
if (!defined('ABSPATH')) {
    exit;
}

class SkillsManagement {
    
    private $table_name;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'kizumi_skills';
        
        // 钩子
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_ajax_save_skill', array($this, 'ajax_save_skill'));
        add_action('wp_ajax_delete_skill', array($this, 'ajax_delete_skill'));
        add_action('wp_ajax_get_skill', array($this, 'ajax_get_skill'));
        add_action('wp_ajax_reset_skills_data', array($this, 'ajax_reset_skills_data'));
        add_action('wp_ajax_batch_delete_skills', array($this, 'ajax_batch_delete_skills'));
        add_action('wp_ajax_batch_toggle_status', array($this, 'ajax_batch_toggle_status'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // 激活主题时创建表
        register_activation_hook(__FILE__, array($this, 'create_table'));
    }
    
    public function init() {
        // 检查表是否存在，不存在则创建
        $this->maybe_create_table();
    }
    
    /**
     * 创建数据库表
     */
    public function create_table() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE {$this->table_name} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            skill_id varchar(50) NOT NULL,
            name varchar(100) NOT NULL,
            description text,
            icon varchar(100),
            category varchar(50) NOT NULL,
            level varchar(20) NOT NULL,
            experience_years int(11) DEFAULT 0,
            experience_months int(11) DEFAULT 0,
            projects text,
            color varchar(7) DEFAULT '#3b82f6',
            sort_order int(11) DEFAULT 0,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY skill_id (skill_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // 插入默认数据
        $this->insert_default_data();
    }
    
    /**
     * 检查并创建表
     */
    private function maybe_create_table() {
        global $wpdb;
        
        if ($wpdb->get_var("SHOW TABLES LIKE '{$this->table_name}'") != $this->table_name) {
            $this->create_table();
        }
    }
    
    /**
     * 插入默认数据
     */
    private function insert_default_data() {
        global $wpdb;
        
        // 检查是否已有数据
        $count = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name}");
        if ($count > 0) {
            return;
        }
        
        $default_skills = [
            // Frontend Skills
            [
                'skill_id' => 'javascript',
                'name' => 'JavaScript',
                'description' => '现代JavaScript开发，包括ES6+语法、异步编程、模块化开发等。',
                'icon' => 'fab fa-js-square',
                'category' => 'frontend',
                'level' => 'advanced',
                'experience_years' => 3,
                'experience_months' => 6,
                'projects' => 'mizuki-blog,portfolio-website,data-visualization-tool',
                'color' => '#F7DF1E',
                'sort_order' => 1
            ],
            [
                'skill_id' => 'typescript',
                'name' => 'TypeScript',
                'description' => '类型安全的JavaScript超集，提升代码质量和开发效率。',
                'icon' => 'fab fa-js-square',
                'category' => 'frontend',
                'level' => 'advanced',
                'experience_years' => 2,
                'experience_months' => 8,
                'projects' => 'mizuki-blog,portfolio-website,task-manager-app',
                'color' => '#3178C6',
                'sort_order' => 2
            ],
            [
                'skill_id' => 'react',
                'name' => 'React',
                'description' => '构建用户界面的JavaScript库，包括Hooks、Context、状态管理等。',
                'icon' => 'fab fa-react',
                'category' => 'frontend',
                'level' => 'advanced',
                'experience_years' => 2,
                'experience_months' => 10,
                'projects' => 'portfolio-website,task-manager-app',
                'color' => '#61DAFB',
                'sort_order' => 3
            ],
            [
                'skill_id' => 'vue',
                'name' => 'Vue.js',
                'description' => '渐进式JavaScript框架，易学易用，适合快速开发。',
                'icon' => 'fab fa-vuejs',
                'category' => 'frontend',
                'level' => 'intermediate',
                'experience_years' => 1,
                'experience_months' => 8,
                'projects' => 'data-visualization-tool',
                'color' => '#4FC08D',
                'sort_order' => 4
            ],
            [
                'skill_id' => 'angular',
                'name' => 'Angular',
                'description' => 'Google开发的企业级前端框架，功能完整的单页应用解决方案。',
                'icon' => 'fab fa-angular',
                'category' => 'frontend',
                'level' => 'beginner',
                'experience_years' => 0,
                'experience_months' => 9,
                'projects' => 'enterprise-dashboard',
                'color' => '#DD0031',
                'sort_order' => 5
            ],
            [
                'skill_id' => 'nextjs',
                'name' => 'Next.js',
                'description' => 'React的生产级框架，支持SSR、SSG和全栈开发。',
                'icon' => 'fas fa-code',
                'category' => 'frontend',
                'level' => 'intermediate',
                'experience_years' => 1,
                'experience_months' => 4,
                'projects' => 'e-commerce-frontend,blog-platform',
                'color' => '#000000',
                'sort_order' => 6
            ],
            [
                'skill_id' => 'nuxtjs',
                'name' => 'Nuxt.js',
                'description' => 'Vue.js的直观框架，支持服务端渲染和静态站点生成。',
                'icon' => 'fab fa-vuejs',
                'category' => 'frontend',
                'level' => 'beginner',
                'experience_years' => 0,
                'experience_months' => 6,
                'projects' => 'vue-ssr-app',
                'color' => '#00DC82',
                'sort_order' => 7
            ],
            [
                'skill_id' => 'astro',
                'name' => 'Astro',
                'description' => '现代静态站点生成器，支持多框架集成和优秀的性能。',
                'icon' => 'fas fa-rocket',
                'category' => 'frontend',
                'level' => 'advanced',
                'experience_years' => 1,
                'experience_months' => 2,
                'projects' => 'mizuki-blog',
                'color' => '#FF5D01',
                'sort_order' => 8
            ],
            [
                'skill_id' => 'tailwindcss',
                'name' => 'Tailwind CSS',
                'description' => '实用优先的CSS框架，快速构建现代化用户界面。',
                'icon' => 'fab fa-css3-alt',
                'category' => 'frontend',
                'level' => 'advanced',
                'experience_years' => 2,
                'experience_months' => 0,
                'projects' => 'mizuki-blog,portfolio-website',
                'color' => '#06B6D4',
                'sort_order' => 9
            ],
            [
                'skill_id' => 'sass',
                'name' => 'Sass/SCSS',
                'description' => 'CSS预处理器，提供变量、嵌套、混合等高级功能。',
                'icon' => 'fab fa-sass',
                'category' => 'frontend',
                'level' => 'intermediate',
                'experience_years' => 2,
                'experience_months' => 3,
                'projects' => 'legacy-website,component-library',
                'color' => '#CF649A',
                'sort_order' => 10
            ],
            [
                'skill_id' => 'webpack',
                'name' => 'Webpack',
                'description' => '现代JavaScript应用的静态模块打包器。',
                'icon' => 'fas fa-cube',
                'category' => 'frontend',
                'level' => 'intermediate',
                'experience_years' => 1,
                'experience_months' => 10,
                'projects' => 'custom-build-tool,spa-application',
                'color' => '#8DD6F9',
                'sort_order' => 11
            ],
            [
                'skill_id' => 'vite',
                'name' => 'Vite',
                'description' => '下一代前端构建工具，快速的冷启动和热更新。',
                'icon' => 'fas fa-bolt',
                'category' => 'frontend',
                'level' => 'intermediate',
                'experience_years' => 1,
                'experience_months' => 2,
                'projects' => 'vue-project,react-project',
                'color' => '#646CFF',
                'sort_order' => 12
            ],

            // Backend Skills
            [
                'skill_id' => 'nodejs',
                'name' => 'Node.js',
                'description' => '基于Chrome V8引擎的JavaScript运行时，用于服务端开发。',
                'icon' => 'fab fa-node-js',
                'category' => 'backend',
                'level' => 'intermediate',
                'experience_years' => 2,
                'experience_months' => 3,
                'projects' => 'data-visualization-tool,e-commerce-platform',
                'color' => '#339933',
                'sort_order' => 13
            ],
            [
                'skill_id' => 'python',
                'name' => 'Python',
                'description' => '通用编程语言，适用于Web开发、数据分析、机器学习等。',
                'icon' => 'fab fa-python',
                'category' => 'backend',
                'level' => 'intermediate',
                'experience_years' => 1,
                'experience_months' => 10,
                'projects' => 'data-analysis,web-scraping',
                'color' => '#3776AB',
                'sort_order' => 14
            ],
            [
                'skill_id' => 'java',
                'name' => 'Java',
                'description' => '企业级应用开发的主流编程语言，跨平台、面向对象。',
                'icon' => 'fab fa-java',
                'category' => 'backend',
                'level' => 'intermediate',
                'experience_years' => 2,
                'experience_months' => 0,
                'projects' => 'enterprise-system,microservices-api',
                'color' => '#ED8B00',
                'sort_order' => 15
            ],
            [
                'skill_id' => 'csharp',
                'name' => 'C#',
                'description' => 'Microsoft开发的现代面向对象编程语言，适用于.NET生态系统。',
                'icon' => 'fas fa-code',
                'category' => 'backend',
                'level' => 'intermediate',
                'experience_years' => 1,
                'experience_months' => 6,
                'projects' => 'desktop-application,web-api',
                'color' => '#239120',
                'sort_order' => 16
            ],
            [
                'skill_id' => 'go',
                'name' => 'Go',
                'description' => 'Google开发的高效编程语言，适用于云原生和微服务开发。',
                'icon' => 'fas fa-code',
                'category' => 'backend',
                'level' => 'beginner',
                'experience_years' => 0,
                'experience_months' => 8,
                'projects' => 'microservice-demo',
                'color' => '#00ADD8',
                'sort_order' => 17
            ],
            [
                'skill_id' => 'rust',
                'name' => 'Rust',
                'description' => '系统级编程语言，注重安全性、速度和并发性，无垃圾回收器。',
                'icon' => 'fas fa-cog',
                'category' => 'backend',
                'level' => 'beginner',
                'experience_years' => 0,
                'experience_months' => 6,
                'projects' => 'system-tool,performance-critical-app',
                'color' => '#CE422B',
                'sort_order' => 18
            ],
            [
                'skill_id' => 'cpp',
                'name' => 'C++',
                'description' => '高性能系统编程语言，广泛用于游戏开发、系统软件和嵌入式开发。',
                'icon' => 'fas fa-code',
                'category' => 'backend',
                'level' => 'intermediate',
                'experience_years' => 1,
                'experience_months' => 4,
                'projects' => 'game-engine,system-optimization',
                'color' => '#00599C',
                'sort_order' => 19
            ],
            [
                'skill_id' => 'c',
                'name' => 'C',
                'description' => '底层系统编程语言，操作系统和嵌入式系统开发的基础。',
                'icon' => 'fas fa-code',
                'category' => 'backend',
                'level' => 'intermediate',
                'experience_years' => 1,
                'experience_months' => 2,
                'projects' => 'embedded-system,kernel-module',
                'color' => '#A8B9CC',
                'sort_order' => 20
            ],
            [
                'skill_id' => 'kotlin',
                'name' => 'Kotlin',
                'description' => 'JetBrains开发的现代编程语言，与Java完全兼容，Android开发首选。',
                'icon' => 'fab fa-android',
                'category' => 'backend',
                'level' => 'beginner',
                'experience_years' => 0,
                'experience_months' => 8,
                'projects' => 'android-app,kotlin-backend',
                'color' => '#7F52FF',
                'sort_order' => 21
            ],
            [
                'skill_id' => 'swift',
                'name' => 'Swift',
                'description' => 'Apple开发的现代编程语言，用于iOS、macOS、watchOS和tvOS开发。',
                'icon' => 'fab fa-apple',
                'category' => 'backend',
                'level' => 'beginner',
                'experience_years' => 0,
                'experience_months' => 6,
                'projects' => 'ios-app,macos-tool',
                'color' => '#FA7343',
                'sort_order' => 22
            ],
            [
                'skill_id' => 'ruby',
                'name' => 'Ruby',
                'description' => '动态、开源的编程语言，注重简洁性和生产力，Rails框架的基础。',
                'icon' => 'fas fa-gem',
                'category' => 'backend',
                'level' => 'beginner',
                'experience_years' => 0,
                'experience_months' => 4,
                'projects' => 'web-prototype',
                'color' => '#CC342D',
                'sort_order' => 23
            ],
            [
                'skill_id' => 'php',
                'name' => 'PHP',
                'description' => '广泛使用的服务器端脚本语言，特别适合Web开发。',
                'icon' => 'fab fa-php',
                'category' => 'backend',
                'level' => 'intermediate',
                'experience_years' => 1,
                'experience_months' => 6,
                'projects' => 'cms-system,e-commerce-backend',
                'color' => '#777BB4',
                'sort_order' => 24
            ],
            [
                'skill_id' => 'express',
                'name' => 'Express.js',
                'description' => '快速、极简的Node.js Web应用框架。',
                'icon' => 'fab fa-node-js',
                'category' => 'backend',
                'level' => 'intermediate',
                'experience_years' => 1,
                'experience_months' => 8,
                'projects' => 'data-visualization-tool',
                'color' => '#000000',
                'sort_order' => 25
            ],
            [
                'skill_id' => 'spring',
                'name' => 'Spring Boot',
                'description' => 'Java生态系统中最流行的企业级应用开发框架。',
                'icon' => 'fas fa-leaf',
                'category' => 'backend',
                'level' => 'intermediate',
                'experience_years' => 1,
                'experience_months' => 4,
                'projects' => 'enterprise-system,rest-api',
                'color' => '#6DB33F',
                'sort_order' => 26
            ],
            [
                'skill_id' => 'django',
                'name' => 'Django',
                'description' => 'Python的高级Web框架，快速开发、简洁实用的设计。',
                'icon' => 'fab fa-python',
                'category' => 'backend',
                'level' => 'beginner',
                'experience_years' => 0,
                'experience_months' => 6,
                'projects' => 'blog-backend',
                'color' => '#092E20',
                'sort_order' => 27
            ],

            // Database Skills
            [
                'skill_id' => 'mysql',
                'name' => 'MySQL',
                'description' => '世界上最流行的开源关系型数据库管理系统，广泛用于Web应用。',
                'icon' => 'fas fa-database',
                'category' => 'database',
                'level' => 'advanced',
                'experience_years' => 2,
                'experience_months' => 6,
                'projects' => 'e-commerce-platform,blog-system',
                'color' => '#4479A1',
                'sort_order' => 28
            ],
            [
                'skill_id' => 'postgresql',
                'name' => 'PostgreSQL',
                'description' => '强大的开源关系型数据库管理系统。',
                'icon' => 'fas fa-database',
                'category' => 'database',
                'level' => 'intermediate',
                'experience_years' => 1,
                'experience_months' => 5,
                'projects' => 'e-commerce-platform',
                'color' => '#336791',
                'sort_order' => 29
            ],
            [
                'skill_id' => 'redis',
                'name' => 'Redis',
                'description' => '高性能的内存数据结构存储，用作数据库、缓存和消息代理。',
                'icon' => 'fas fa-memory',
                'category' => 'database',
                'level' => 'intermediate',
                'experience_years' => 1,
                'experience_months' => 3,
                'projects' => 'e-commerce-platform,real-time-chat',
                'color' => '#DC382D',
                'sort_order' => 30
            ],
            [
                'skill_id' => 'mongodb',
                'name' => 'MongoDB',
                'description' => '面向文档的NoSQL数据库，灵活的数据模型。',
                'icon' => 'fas fa-leaf',
                'category' => 'database',
                'level' => 'intermediate',
                'experience_years' => 1,
                'experience_months' => 2,
                'projects' => 'content-management,api-backend',
                'color' => '#47A248',
                'sort_order' => 31
            ],
            [
                'skill_id' => 'sqlite',
                'name' => 'SQLite',
                'description' => '轻量级的嵌入式关系型数据库，适用于移动应用和小型项目。',
                'icon' => 'fas fa-database',
                'category' => 'database',
                'level' => 'intermediate',
                'experience_years' => 1,
                'experience_months' => 8,
                'projects' => 'mobile-app,desktop-tool',
                'color' => '#003B57',
                'sort_order' => 32
            ],
            [
                'skill_id' => 'firebase',
                'name' => 'Firebase',
                'description' => 'Google的移动和Web应用开发平台，提供实时数据库和认证服务。',
                'icon' => 'fas fa-fire',
                'category' => 'database',
                'level' => 'intermediate',
                'experience_years' => 0,
                'experience_months' => 10,
                'projects' => 'task-manager-app',
                'color' => '#FFCA28',
                'sort_order' => 33
            ],

            // Tools
            [
                'skill_id' => 'git',
                'name' => 'Git',
                'description' => '分布式版本控制系统，代码管理和团队协作必备工具。',
                'icon' => 'fab fa-git-alt',
                'category' => 'tools',
                'level' => 'advanced',
                'experience_years' => 3,
                'experience_months' => 0,
                'projects' => '所有项目',
                'color' => '#F05032',
                'sort_order' => 34
            ],
            [
                'skill_id' => 'vscode',
                'name' => 'VS Code',
                'description' => '轻量级但功能强大的代码编辑器，丰富的插件生态。',
                'icon' => 'fas fa-code',
                'category' => 'tools',
                'level' => 'expert',
                'experience_years' => 3,
                'experience_months' => 6,
                'projects' => '所有开发项目',
                'color' => '#007ACC',
                'sort_order' => 35
            ],
            [
                'skill_id' => 'webstorm',
                'name' => 'WebStorm',
                'description' => 'JetBrains开发的专业JavaScript和Web开发IDE，智能代码辅助。',
                'icon' => 'fas fa-code',
                'category' => 'tools',
                'level' => 'advanced',
                'experience_years' => 2,
                'experience_months' => 0,
                'projects' => 'react-project,vue-project',
                'color' => '#00CDD7',
                'sort_order' => 36
            ],
            [
                'skill_id' => 'intellij',
                'name' => 'IntelliJ IDEA',
                'description' => 'JetBrains旗舰IDE，Java开发的首选工具，强大的智能编码辅助。',
                'icon' => 'fas fa-code',
                'category' => 'tools',
                'level' => 'intermediate',
                'experience_years' => 1,
                'experience_months' => 8,
                'projects' => 'java-enterprise,spring-boot-app',
                'color' => '#000000',
                'sort_order' => 37
            ],
            [
                'skill_id' => 'pycharm',
                'name' => 'PyCharm',
                'description' => 'JetBrains专业Python IDE，提供智能代码分析和调试功能。',
                'icon' => 'fab fa-python',
                'category' => 'tools',
                'level' => 'intermediate',
                'experience_years' => 1,
                'experience_months' => 4,
                'projects' => 'python-web-app,data-analysis',
                'color' => '#21D789',
                'sort_order' => 38
            ],
            [
                'skill_id' => 'docker',
                'name' => 'Docker',
                'description' => '容器化平台，简化应用部署和环境管理。',
                'icon' => 'fab fa-docker',
                'category' => 'tools',
                'level' => 'intermediate',
                'experience_years' => 1,
                'experience_months' => 0,
                'projects' => 'microservices,deployment',
                'color' => '#2496ED',
                'sort_order' => 39
            ],
            [
                'skill_id' => 'kubernetes',
                'name' => 'Kubernetes',
                'description' => '容器编排平台，用于自动化部署、扩展和管理容器化应用。',
                'icon' => 'fas fa-dharmachakra',
                'category' => 'tools',
                'level' => 'beginner',
                'experience_years' => 0,
                'experience_months' => 4,
                'projects' => 'microservices-deployment',
                'color' => '#326CE5',
                'sort_order' => 40
            ],
            [
                'skill_id' => 'nginx',
                'name' => 'Nginx',
                'description' => '高性能的Web服务器和反向代理服务器。',
                'icon' => 'fas fa-server',
                'category' => 'tools',
                'level' => 'intermediate',
                'experience_years' => 1,
                'experience_months' => 2,
                'projects' => 'web-server-config,load-balancer',
                'color' => '#009639',
                'sort_order' => 41
            ],
            [
                'skill_id' => 'apache',
                'name' => 'Apache HTTP Server',
                'description' => '世界上最流行的Web服务器软件，稳定可靠的HTTP服务器。',
                'icon' => 'fas fa-server',
                'category' => 'tools',
                'level' => 'intermediate',
                'experience_years' => 1,
                'experience_months' => 6,
                'projects' => 'traditional-web-server,php-hosting',
                'color' => '#D22128',
                'sort_order' => 42
            ],
            [
                'skill_id' => 'aws',
                'name' => 'AWS',
                'description' => '亚马逊云服务平台，提供全面的云计算解决方案。',
                'icon' => 'fab fa-aws',
                'category' => 'tools',
                'level' => 'intermediate',
                'experience_years' => 1,
                'experience_months' => 0,
                'projects' => 'cloud-deployment,serverless-app',
                'color' => '#FF9900',
                'sort_order' => 43
            ],
            [
                'skill_id' => 'linux',
                'name' => 'Linux',
                'description' => '开源操作系统，服务器部署和开发环境的首选。',
                'icon' => 'fab fa-linux',
                'category' => 'tools',
                'level' => 'intermediate',
                'experience_years' => 2,
                'experience_months' => 0,
                'projects' => 'server-management,shell-scripting',
                'color' => '#FCC624',
                'sort_order' => 44
            ],
            [
                'skill_id' => 'postman',
                'name' => 'Postman',
                'description' => 'API开发和测试工具，简化API的设计、测试和文档编写。',
                'icon' => 'fas fa-paper-plane',
                'category' => 'tools',
                'level' => 'intermediate',
                'experience_years' => 1,
                'experience_months' => 8,
                'projects' => 'api-testing,api-documentation',
                'color' => '#FF6C37',
                'sort_order' => 45
            ],
            [
                'skill_id' => 'figma',
                'name' => 'Figma',
                'description' => '协作式界面设计工具，用于UI/UX设计和原型制作。',
                'icon' => 'fab fa-figma',
                'category' => 'tools',
                'level' => 'intermediate',
                'experience_years' => 1,
                'experience_months' => 6,
                'projects' => 'ui-design,prototyping',
                'color' => '#F24E1E',
                'sort_order' => 46
            ],
            [
                'skill_id' => 'photoshop',
                'name' => 'Photoshop',
                'description' => '专业的图像编辑和设计软件。',
                'icon' => 'fas fa-image',
                'category' => 'tools',
                'level' => 'intermediate',
                'experience_years' => 2,
                'experience_months' => 6,
                'projects' => 'ui-design,image-processing',
                'color' => '#31A8FF',
                'sort_order' => 47
            ],

            // Other Skills
            [
                'skill_id' => 'graphql',
                'name' => 'GraphQL',
                'description' => 'API查询语言和运行时，提供更高效、强大和灵活的数据获取方式。',
                'icon' => 'fas fa-project-diagram',
                'category' => 'other',
                'level' => 'beginner',
                'experience_years' => 0,
                'experience_months' => 6,
                'projects' => 'modern-api',
                'color' => '#E10098',
                'sort_order' => 48
            ],
            [
                'skill_id' => 'elasticsearch',
                'name' => 'Elasticsearch',
                'description' => '分布式搜索和分析引擎，用于全文搜索和数据分析。',
                'icon' => 'fas fa-search',
                'category' => 'other',
                'level' => 'beginner',
                'experience_years' => 0,
                'experience_months' => 4,
                'projects' => 'search-system',
                'color' => '#005571',
                'sort_order' => 49
            ],
            [
                'skill_id' => 'jest',
                'name' => 'Jest',
                'description' => 'JavaScript测试框架，专注于简洁性和易用性。',
                'icon' => 'fas fa-vial',
                'category' => 'other',
                'level' => 'intermediate',
                'experience_years' => 1,
                'experience_months' => 2,
                'projects' => 'unit-testing,integration-testing',
                'color' => '#C21325',
                'sort_order' => 50
            ],
            [
                'skill_id' => 'cypress',
                'name' => 'Cypress',
                'description' => '现代Web应用的端到端测试框架。',
                'icon' => 'fas fa-bug',
                'category' => 'other',
                'level' => 'beginner',
                'experience_years' => 0,
                'experience_months' => 8,
                'projects' => 'e2e-testing',
                'color' => '#17202C',
                'sort_order' => 51
            ],
            // New Frontend Skills
            [
                'skill_id' => 'svelte', 
                'name' => 'Svelte', 
                'description' => '一款轻量级、高性能的JavaScript框架，将组件编译为原生JavaScript，实现更小的打包体积和更快的运行速度。', 
                'icon' => 'fas fa-code', 
                'category' => 'frontend', 
                'level' => 'intermediate', 
                'experience_years' => 1, 
                'experience_months' => 0, 
                'projects' => 'svelte-app,personal-website', 
                'color' => '#FF3E00', 
                'sort_order' => 52
            ],
            [
                'skill_id' => 'bootstrap', 
                'name' => 'Bootstrap', 
                'description' => '最流行的HTML、CSS和JavaScript框架，用于开发响应式、移动设备优先的Web项目。', 
                'icon' => 'fab fa-bootstrap', 
                'category' => 'frontend', 
                'level' => 'advanced', 
                'experience_years' => 3, 
                'experience_months' => 0, 
                'projects' => '各种Web项目', 
                'color' => '#7952B3', 
                'sort_order' => 53
            ],
            [
                'skill_id' => 'jquery', 
                'name' => 'jQuery', 
                'description' => '一个快速、小型、功能丰富的JavaScript库，简化了HTML文档遍历、事件处理、动画和Ajax交互。', 
                'icon' => 'fab fa-js', 
                'category' => 'frontend', 
                'level' => 'advanced', 
                'experience_years' => 4, 
                'experience_months' => 0, 
                'projects' => '旧项目维护,快速原型开发', 
                'color' => '#0769AD', 
                'sort_order' => 54
            ],

            // New Backend Skills
            [
                'skill_id' => 'nestjs', 
                'name' => 'NestJS', 
                'description' => '一个用于构建高效、可伸缩的Node.js服务器端应用程序的渐进式Node.js框架。', 
                'icon' => 'fas fa-server', 
                'category' => 'backend', 
                'level' => 'intermediate', 
                'experience_years' => 1, 
                'experience_months' => 6, 
                'projects' => '企业级后端服务,微服务', 
                'color' => '#E0234E', 
                'sort_order' => 55
            ],
            [
                'skill_id' => 'laravel', 
                'name' => 'Laravel', 
                'description' => '一个富有表现力、优雅的PHP Web框架，提供构建大型、健壮应用程序所需的所有工具。', 
                'icon' => 'fab fa-laravel', 
                'category' => 'backend', 
                'level' => 'intermediate', 
                'experience_years' => 2, 
                'experience_months' => 0, 
                'projects' => 'CMS系统,电商平台', 
                'color' => '#FF2D20', 
                'sort_order' => 56
            ],
            [
                'skill_id' => 'flask', 
                'name' => 'Flask', 
                'description' => '一个轻量级的Python Web框架，适用于快速开发小型到中型Web应用程序。', 
                'icon' => 'fab fa-python', 
                'category' => 'backend', 
                'level' => 'beginner', 
                'experience_years' => 0, 
                'experience_months' => 10, 
                'projects' => 'RESTful API,小型工具', 
                'color' => '#000000', 
                'sort_order' => 57
            ],

            // New Database Skills
            [
                'skill_id' => 'oracle', 
                'name' => 'Oracle Database', 
                'description' => '全球领先的企业级关系型数据库管理系统，提供高性能、高可用性和安全性。', 
                'icon' => 'fas fa-database', 
                'category' => 'database', 
                'level' => 'intermediate', 
                'experience_years' => 2, 
                'experience_months' => 0, 
                'projects' => '大型企业应用', 
                'color' => '#F80000', 
                'sort_order' => 58
            ],
            [
                'skill_id' => 'sqlserver', 
                'name' => 'SQL Server', 
                'description' => 'Microsoft开发的关系型数据库管理系统，广泛应用于企业级数据管理和商业智能。', 
                'icon' => 'fas fa-database', 
                'category' => 'database', 
                'level' => 'intermediate', 
                'experience_years' => 1, 
                'experience_months' => 8, 
                'projects' => 'Windows平台应用,数据仓库', 
                'color' => '#CC2927', 
                'sort_order' => 59
            ],
            // More Database Skills
            [
                'skill_id' => 'mongodb_atlas',
                'name' => 'MongoDB Atlas',
                'description' => 'MongoDB提供的全球云数据库服务，提供可伸缩、高性能的NoSQL数据库解决方案。',
                'icon' => 'fas fa-database',
                'category' => 'database',
                'level' => 'intermediate',
                'experience_years' => 1,
                'experience_months' => 3,
                'projects' => '云原生应用,大数据',
                'color' => '#47A248',
                'sort_order' => 63
            ],
            [
                'skill_id' => 'cassandra',
                'name' => 'Apache Cassandra',
                'description' => '一个高度可伸缩的NoSQL数据库，用于处理大量分布式数据，提供高可用性和线性伸缩性。',
                'icon' => 'fas fa-database',
                'category' => 'database',
                'level' => 'beginner',
                'experience_years' => 0,
                'experience_months' => 9,
                'projects' => '大数据存储,实时数据',
                'color' => '#336699',
                'sort_order' => 64
            ],
            [
                'skill_id' => 'mariadb',
                'name' => 'MariaDB',
                'description' => '一个由MySQL社区开发的开源关系型数据库管理系统，旨在保持与MySQL的高度兼容性。',
                'icon' => 'fas fa-database',
                'category' => 'database',
                'level' => 'advanced',
                'experience_years' => 3,
                'experience_months' => 0,
                'projects' => 'Web应用,数据仓库',
                'color' => '#003545',
                'sort_order' => 65
            ],

            // New Tools
            [
                'skill_id' => 'jenkins', 
                'name' => 'Jenkins', 
                'description' => '领先的开源自动化服务器，支持持续集成、持续交付和部署。', 
                'icon' => 'fab fa-jenkins', 
                'category' => 'tools', 
                'level' => 'intermediate', 
                'experience_years' => 1, 
                'experience_months' => 4, 
                'projects' => 'CI/CD流水线', 
                'color' => '#D24939', 
                'sort_order' => 60
            ],
            [
                'skill_id' => 'jira', 
                'name' => 'Jira', 
                'description' => 'Atlassian开发的项目管理和问题跟踪工具，广泛用于敏捷开发和DevOps。', 
                'icon' => 'fab fa-jira', 
                'category' => 'tools', 
                'level' => 'intermediate', 
                'experience_years' => 1, 
                'experience_months' => 0, 
                'projects' => '项目管理,缺陷跟踪', 
                'color' => '#0052CC', 
                'sort_order' => 61
            ],
            [
                'skill_id' => 'confluence', 
                'name' => 'Confluence', 
                'description' => 'Atlassian的企业维基和协作平台，用于知识管理和团队协作。', 
                'icon' => 'fab fa-confluence', 
                'category' => 'tools', 
                'level' => 'intermediate', 
                'experience_years' => 1, 
                'experience_months' => 0, 
                'projects' => '文档管理,团队协作', 
                'color' => '#172B4D', 
                'sort_order' => 62
            ]
        ];
        
        foreach ($default_skills as $skill) {
            $wpdb->insert($this->table_name, $skill);
        }
    }
    
    /**
     * 添加管理菜单
     */
    public function add_admin_menu() {
        add_menu_page(
            '技能管理',
            '技能管理',
            'manage_options',
            'skills-management',
            array($this, 'admin_page'),
            'dashicons-star-filled',
            30
        );
    }
    
    /**
     * 管理页面
     */
    public function admin_page() {
        global $wpdb;
        
        // 获取所有技能
        $skills = $wpdb->get_results("SELECT * FROM {$this->table_name} ORDER BY sort_order ASC, id ASC");
        
        ?>
        <div class="wrap">
            <h1>技能管理</h1>
            
            <div class="skills-management-container">
                <div class="skills-actions">
                    <div class="main-actions">
                        <button type="button" class="button button-primary" id="add-skill-btn">添加新技能</button>
                        <button type="button" class="button button-secondary" id="reset-skills-btn" style="background-color: #dc3545; border-color: #dc3545; color: white; margin-left: 10px;">重置技能数据</button>
                        
                        <!-- 批量操作按钮 - 在重置按钮右边 -->
                        <div class="batch-buttons">
                            <span class="batch-info">已选择 <span id="selected-count">0</span> 项</span>
                            <button type="button" class="button button-primary" id="batch-enable-btn">批量启用</button>
                            <button type="button" class="button button-secondary" id="batch-disable-btn">批量禁用</button>
                            <button type="button" class="button button-danger" id="batch-delete-btn">批量删除</button>
                        </div>
                    </div>
                </div>
                
                <div class="skills-list">
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th class="check-column">
                                    <input type="checkbox" id="select-all-skills">
                                </th>
                                <th>排序</th>
                                <th>图标</th>
                                <th>技能名称</th>
                                <th>分类</th>
                                <th>等级</th>
                                <th>经验</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody id="skills-table-body">
                            <?php foreach ($skills as $skill): ?>
                            <tr data-skill-id="<?php echo $skill->id; ?>">
                                <td class="check-column">
                                    <input type="checkbox" class="skill-checkbox" value="<?php echo $skill->id; ?>">
                                </td>
                                <td><?php echo $skill->sort_order; ?></td>
                                <td>
                                    <i class="<?php echo esc_attr($skill->icon); ?>" style="color: <?php echo esc_attr($skill->color); ?>; font-size: 20px;"></i>
                                </td>
                                <td><strong><?php echo esc_html($skill->name); ?></strong></td>
                                <td><?php echo $this->get_category_name($skill->category); ?></td>
                                <td><?php echo $this->get_level_name($skill->level); ?></td>
                                <td><?php echo $skill->experience_years; ?>年<?php echo $skill->experience_months; ?>个月</td>
                                <td>
                                    <span class="status-<?php echo $skill->status; ?>">
                                        <?php echo $skill->status === 'active' ? '启用' : '禁用'; ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="button button-small edit-skill" data-id="<?php echo $skill->id; ?>">编辑</button>
                                    <button type="button" class="button button-small delete-skill" data-id="<?php echo $skill->id; ?>">删除</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- 技能编辑模态框 -->
            <div id="skill-modal" class="skill-modal" style="display: none;">
                <div class="skill-modal-content">
                    <div class="skill-modal-header">
                        <h2 id="modal-title">添加技能</h2>
                        <span class="skill-modal-close">&times;</span>
                    </div>
                    <div class="skill-modal-body">
                        <form id="skill-form">
                            <input type="hidden" id="skill-id" name="skill_id_db" value="">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="skill-unique-id">技能ID *</label>
                                    <input type="text" id="skill-unique-id" name="skill_id" required>
                                    <small>唯一标识符，只能包含字母、数字和下划线</small>
                                </div>
                                <div class="form-group">
                                    <label for="skill-name">技能名称 *</label>
                                    <input type="text" id="skill-name" name="name" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="skill-description">描述</label>
                                <textarea id="skill-description" name="description" rows="3"></textarea>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="skill-icon">图标类名</label>
                                    <input type="text" id="skill-icon" name="icon" placeholder="如: fab fa-html5">
                                    <small>FontAwesome图标类名</small>
                                </div>
                                <div class="form-group">
                                    <label for="skill-color">颜色</label>
                                    <input type="color" id="skill-color" name="color" value="#3b82f6">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="skill-category">分类 *</label>
                                    <select id="skill-category" name="category" required>
                                        <option value="frontend">前端开发</option>
                                        <option value="backend">后端开发</option>
                                        <option value="database">数据库</option>
                                        <option value="tools">开发工具</option>
                                        <option value="other">其他技能</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="skill-level">等级 *</label>
                                    <select id="skill-level" name="level" required>
                                        <option value="beginner">初级</option>
                                        <option value="intermediate">中级</option>
                                        <option value="advanced">高级</option>
                                        <option value="expert">专家级</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="skill-years">经验年数</label>
                                    <input type="number" id="skill-years" name="experience_years" min="0" value="0">
                                </div>
                                <div class="form-group">
                                    <label for="skill-months">经验月数</label>
                                    <input type="number" id="skill-months" name="experience_months" min="0" max="11" value="0">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="skill-projects">相关项目</label>
                                    <input type="text" id="skill-projects" name="projects" placeholder="用逗号分隔多个项目">
                                </div>
                                <div class="form-group">
                                    <label for="skill-sort">排序</label>
                                    <input type="number" id="skill-sort" name="sort_order" min="0" value="0">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="skill-status">状态</label>
                                <select id="skill-status" name="status">
                                    <option value="active">启用</option>
                                    <option value="inactive">禁用</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="skill-modal-footer">
                        <button type="button" class="button" id="cancel-skill">取消</button>
                        <button type="button" class="button button-primary" id="save-skill">保存</button>
                    </div>
                </div>
            </div>
        </div>
        
        <style>
        .skills-management-container {
            margin-top: 20px;
        }
        
        .skills-actions {
            margin-bottom: 20px;
        }
        
        .skill-modal {
            position: fixed;
            z-index: 100000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .skill-modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 0;
            border: 1px solid #888;
            width: 80%;
            max-width: 800px;
            border-radius: 4px;
        }
        
        .skill-modal-header {
            padding: 20px;
            background-color: #f1f1f1;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .skill-modal-header h2 {
            margin: 0;
        }
        
        .skill-modal-close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .skill-modal-close:hover {
            color: black;
        }
        
        .skill-modal-body {
            padding: 20px;
        }
        
        .skill-modal-footer {
            padding: 20px;
            background-color: #f1f1f1;
            border-top: 1px solid #ddd;
            text-align: right;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }
        
        .form-group {
            flex: 1;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .form-group small {
            color: #666;
            font-size: 12px;
        }
        
        .status-active {
            color: #46b450;
        }
        
        .status-inactive {
            color: #dc3232;
        }
        
        #reset-skills-btn {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: white !important;
            transition: all 0.3s ease;
        }
        
        #reset-skills-btn:hover {
            background-color: #c82333 !important;
            border-color: #bd2130 !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }
        
        #reset-skills-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
        }
        
        #reset-skills-btn:disabled {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        /* 主要操作区域样式 */
        .main-actions {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        /* 批量操作按钮样式 */
        .batch-buttons {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: 20px;
            flex-wrap: nowrap;
        }
        
        .batch-info {
            font-weight: 600;
            color: #495057;
            margin-right: 5px;
            line-height: 32px;
            white-space: nowrap;
        }
        
        .batch-buttons .button {
            margin: 0 !important;
            height: 32px;
            line-height: 30px;
            padding: 0 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            vertical-align: middle;
            font-size: 13px;
        }
        
        /* 批量启用按钮 */
        #batch-enable-btn {
            background-color: #46b450 !important;
            border-color: #46b450 !important;
            color: white !important;
        }
        
        #batch-enable-btn:hover {
            background-color: #3e9f47 !important;
            border-color: #3e9f47 !important;
        }
        
        /* 批量禁用按钮 */
        #batch-disable-btn {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            color: white !important;
        }
        
        #batch-disable-btn:hover {
            background-color: #5a6268 !important;
            border-color: #545b62 !important;
        }
        
        #batch-delete-btn {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: white !important;
        }
        
        #batch-delete-btn:hover {
            background-color: #c82333 !important;
            border-color: #bd2130 !important;
        }
        
        /* 复选框样式 */
        .check-column {
            width: 40px;
            text-align: center;
            vertical-align: middle;
            padding: 8px 4px !important;
        }
        
        .skill-checkbox, #select-all-skills {
            margin: 0 !important;
            vertical-align: middle;
            width: 16px;
            height: 16px;
            cursor: pointer;
            position: relative;
            top: 0;
        }
        
        /* 表格头部复选框对齐 */
        .wp-list-table thead th.check-column {
            vertical-align: middle !important;
            padding: 8px 4px !important;
            text-align: center !important;
        }
        
        .wp-list-table thead th.check-column input[type="checkbox"] {
            margin: 0 !important;
            vertical-align: middle !important;
            position: relative;
            top: 0 !important;
        }
        
        /* 表格行复选框对齐 */
        .wp-list-table tbody td.check-column {
            vertical-align: middle !important;
            padding: 8px 4px !important;
            text-align: center !important;
        }
        
        .wp-list-table tbody td.check-column input[type="checkbox"] {
            margin: 0 !important;
            vertical-align: middle !important;
            position: relative;
            top: 0 !important;
        }
        
        /* 选中行高亮 */
        tr.selected {
            background-color: #e3f2fd !important;
        }
        
        /* 全选复选框的不确定状态样式 */
        #select-all-skills:indeterminate {
            opacity: 0.5;
        }
        
        /* 响应式设计 */
        @media (max-width: 768px) {
            /* 整体容器边距调整 */
            .skills-management-container {
                margin: 0 -15px;
                padding: 0 15px;
            }
            
            /* 手机端隐藏多选功能 */
            .batch-buttons,
            .wp-list-table th.check-column,
            .wp-list-table td.check-column {
                display: none !important;
            }
            
            /* 主要操作区域在手机端的布局调整 */
            .main-actions {
                flex-direction: row;
                flex-wrap: wrap;
                align-items: center;
                gap: 10px;
                justify-content: flex-start;
            }
            
            /* 手机端按钮区域滚动控制 */
            .skills-actions {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                white-space: nowrap;
                padding: 10px 0;
                margin: 0 -15px 15px -15px;
                padding-left: 15px;
                padding-right: 15px;
            }
            
            .skills-actions .button {
                white-space: nowrap;
                margin-right: 10px;
                display: inline-block;
            }
            
            .skills-actions .button:last-child {
                margin-right: 15px;
            }
            
            /* 隐藏等级和经验列 */
            .wp-list-table th:nth-child(6),  /* 等级列 */
            .wp-list-table td:nth-child(6),
            .wp-list-table th:nth-child(7),  /* 经验列 */
            .wp-list-table td:nth-child(7) {
                display: none;
            }
            
            /* 表格容器滚动 */
            .skills-management-container {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                margin: 0 -10px;
                padding: 0 10px;
            }
            
            /* 表格最小宽度，确保内容不被压缩 */
            .wp-list-table {
                min-width: 800px;
                width: 100%;
                margin-bottom: 10px;
            }
            
            /* 确保表格右侧有足够边距 */
            .wp-list-table td:last-child,
            .wp-list-table th:last-child {
                padding-right: 15px;
            }
            
            /* 操作按钮强制一行显示 */
            .wp-list-table td:nth-child(9) {
                white-space: nowrap;
                min-width: 120px;
                padding: 8px 4px;
            }
            
            .wp-list-table td:nth-child(9) .button {
                display: inline-block;
                margin-right: 4px;
                margin-bottom: 0;
                padding: 4px 8px;
                font-size: 12px;
                flex-shrink: 0;
            }
            
            .wp-list-table td:nth-child(9) .button:last-child {
                margin-right: 0;
            }
        }
        
        /* 桌面端进一步优化 */
        @media (min-width: 769px) {
            .batch-actions {
                justify-content: flex-start;
                align-items: center;
            }
            
            .batch-actions .button {
                flex-shrink: 0;
            }
        }
        
        /* 超小屏幕进一步优化 */
        @media (max-width: 480px) {
            /* 整体容器更大边距 */
            .skills-management-container {
                margin: 0 -20px;
                padding: 0 20px;
            }
            
            /* 隐藏排序列 */
            .wp-list-table th:nth-child(2),
            .wp-list-table td:nth-child(2) {
                display: none;
            }
            
            /* 操作按钮列确保按钮不换行 */
            .wp-list-table td:nth-child(9) {
                min-width: 120px;
                padding-right: 20px;
            }
            
            /* 按钮更紧凑 */
            .wp-list-table td:nth-child(9) .button {
                padding: 3px 6px;
                font-size: 11px;
                margin-right: 3px;
            }
            
            /* 技能名称列自适应 */
            .wp-list-table td:nth-child(4) {
                word-break: break-word;
                max-width: 100px;
            }
            
            /* 分类和状态列更紧凑 */
            .wp-list-table th:nth-child(5), .wp-list-table td:nth-child(5),
            .wp-list-table th:nth-child(8), .wp-list-table td:nth-child(8) {
                font-size: 11px;
                padding: 4px;
            }
            
            /* 批量操作按钮容器边距 */
            .batch-actions {
                margin: 0 -10px 15px -10px;
            }
        }
        </style>
        <?php
    }
    
    /**
     * 加载管理脚本
     */
    public function enqueue_admin_scripts($hook) {
        if ($hook !== 'toplevel_page_skills-management') {
            return;
        }
        
        // 加载Font Awesome 6.4
        wp_enqueue_style('fontawesome-6.4', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
        
        wp_enqueue_script('skills-management-js', get_template_directory_uri() . '/assets/js/skills-management.js', array('jquery'), '1.0.0', true);
        wp_localize_script('skills-management-js', 'skillsAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('skills_management_nonce')
        ));
    }
    
    /**
     * AJAX保存技能
     */
    public function ajax_save_skill() {
        check_ajax_referer('skills_management_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('权限不足');
        }
        
        global $wpdb;
        
        $skill_id_db = intval($_POST['skill_id_db']);
        $data = array(
            'skill_id' => sanitize_text_field($_POST['skill_id']),
            'name' => sanitize_text_field($_POST['name']),
            'description' => sanitize_textarea_field($_POST['description']),
            'icon' => sanitize_text_field($_POST['icon']),
            'category' => sanitize_text_field($_POST['category']),
            'level' => sanitize_text_field($_POST['level']),
            'experience_years' => intval($_POST['experience_years']),
            'experience_months' => intval($_POST['experience_months']),
            'projects' => sanitize_text_field($_POST['projects']),
            'color' => sanitize_hex_color($_POST['color']),
            'sort_order' => intval($_POST['sort_order']),
            'status' => sanitize_text_field($_POST['status'])
        );
        
        if ($skill_id_db > 0) {
            // 更新
            $result = $wpdb->update($this->table_name, $data, array('id' => $skill_id_db));
        } else {
            // 新增
            $result = $wpdb->insert($this->table_name, $data);
        }
        
        if ($result !== false) {
            wp_send_json_success('保存成功');
        } else {
            wp_send_json_error('保存失败');
        }
    }
    
    /**
     * AJAX删除技能
     */
    public function ajax_delete_skill() {
        check_ajax_referer('skills_management_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('权限不足');
        }
        
        global $wpdb;
        
        $skill_id = intval($_POST['skill_id']);
        $result = $wpdb->delete($this->table_name, array('id' => $skill_id));
        
        if ($result !== false) {
            wp_send_json_success('删除成功');
        } else {
            wp_send_json_error('删除失败');
        }
    }
    
    /**
     * AJAX获取技能
     */
    public function ajax_get_skill() {
        check_ajax_referer('skills_management_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('权限不足');
        }
        
        global $wpdb;
        
        $skill_id = intval($_POST['skill_id']);
        $skill = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $skill_id));
        
        if ($skill) {
            wp_send_json_success($skill);
        } else {
            wp_send_json_error('技能不存在');
        }
    }
    
    /**
     * AJAX重置技能数据
     */
    public function ajax_reset_skills_data() {
        check_ajax_referer('skills_management_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('权限不足');
        }
        
        global $wpdb;
        
        try {
            // 清空现有数据
            $wpdb->query("TRUNCATE TABLE {$this->table_name}");
            
            // 重新插入默认数据
            $this->insert_default_data();
            
            wp_send_json_success('技能数据重置成功');
        } catch (Exception $e) {
            wp_send_json_error('重置失败：' . $e->getMessage());
        }
    }
    
    /**
     * AJAX批量删除技能
     */
    public function ajax_batch_delete_skills() {
        check_ajax_referer('skills_management_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('权限不足');
        }
        
        global $wpdb;
        
        $skill_ids = array_map('intval', $_POST['skill_ids']);
        
        if (empty($skill_ids)) {
            wp_send_json_error('请选择要删除的技能');
        }
        
        $placeholders = implode(',', array_fill(0, count($skill_ids), '%d'));
        $sql = "DELETE FROM {$this->table_name} WHERE id IN ($placeholders)";
        
        $result = $wpdb->query($wpdb->prepare($sql, $skill_ids));
        
        if ($result !== false) {
            wp_send_json_success("成功删除 {$result} 个技能");
        } else {
            wp_send_json_error('批量删除失败');
        }
    }
    
    /**
     * AJAX批量切换状态
     */
    public function ajax_batch_toggle_status() {
        check_ajax_referer('skills_management_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('权限不足');
        }
        
        global $wpdb;
        
        $skill_ids = array_map('intval', $_POST['skill_ids']);
        $status = sanitize_text_field($_POST['status']); // 'active' 或 'inactive'
        
        if (empty($skill_ids)) {
            wp_send_json_error('请选择要操作的技能');
        }
        
        if (!in_array($status, ['active', 'inactive'])) {
            wp_send_json_error('无效的状态值');
        }
        
        $placeholders = implode(',', array_fill(0, count($skill_ids), '%d'));
        $sql = "UPDATE {$this->table_name} SET status = %s WHERE id IN ($placeholders)";
        
        $params = array_merge([$status], $skill_ids);
        $result = $wpdb->query($wpdb->prepare($sql, $params));
        
        if ($result !== false) {
            $status_text = $status === 'active' ? '启用' : '禁用';
            wp_send_json_success("成功{$status_text} {$result} 个技能");
        } else {
            wp_send_json_error('批量操作失败');
        }
    }
    
    /**
     * 获取分类名称
     */
    private function get_category_name($category) {
        $categories = array(
            'frontend' => '前端开发',
            'backend' => '后端开发',
            'database' => '数据库',
            'tools' => '开发工具',
            'other' => '其他技能'
        );
        
        return isset($categories[$category]) ? $categories[$category] : $category;
    }
    
    /**
     * 获取等级名称
     */
    private function get_level_name($level) {
        $levels = array(
            'beginner' => '初级',
            'intermediate' => '中级',
            'advanced' => '高级',
            'expert' => '专家级'
        );
        
        return isset($levels[$level]) ? $levels[$level] : $level;
    }
    
    /**
     * 获取技能数据（供前端调用）
     */
    public static function get_skills_data() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'kizumi_skills';
        
        // 检查表是否存在，不存在则创建
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
            // 创建表和插入默认数据
            self::create_table_static();
        }
        
        $skills = $wpdb->get_results("SELECT * FROM {$table_name} WHERE status = 'active' ORDER BY sort_order ASC, id ASC");
        
        if (empty($skills)) {
            return array();
        }
        
        $formatted_skills = array();
        foreach ($skills as $skill) {
            $projects = !empty($skill->projects) ? explode(',', $skill->projects) : array();
            $projects = array_map('trim', $projects);
            
            $formatted_skills[] = array(
                'id' => $skill->skill_id,
                'name' => $skill->name,
                'description' => $skill->description,
                'icon' => $skill->icon,
                'category' => $skill->category,
                'level' => $skill->level,
                'experience' => array(
                    'years' => intval($skill->experience_years),
                    'months' => intval($skill->experience_months)
                ),
                'projects' => $projects,
                'color' => $skill->color
            );
        }
        
        return $formatted_skills;
    }
    
    /**
     * 静态方法创建表（供静态调用）
     */
    public static function create_table_static() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'Kizumi_skills';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE {$table_name} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            skill_id varchar(50) NOT NULL,
            name varchar(100) NOT NULL,
            description text,
            icon varchar(100),
            category varchar(50) NOT NULL,
            level varchar(20) NOT NULL,
            experience_years int(11) DEFAULT 0,
            experience_months int(11) DEFAULT 0,
            projects text,
            color varchar(7) DEFAULT '#3b82f6',
            sort_order int(11) DEFAULT 0,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY skill_id (skill_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // 插入默认数据
        self::insert_default_data_static();
    }
    
    /**
     * 静态方法插入默认数据
     */
    public static function insert_default_data_static() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'Kizumi_skills';
        
        // 检查是否已有数据
        $count = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
        if ($count > 0) {
            return;
        }
        
        $default_skills = [
            [
                'skill_id' => 'html5',
                'name' => 'HTML5',
                'description' => '现代Web标准，语义化标签、多媒体支持等。',
                'icon' => 'fab fa-html5',
                'category' => 'frontend',
                'level' => 'expert',
                'experience_years' => 4,
                'experience_months' => 0,
                'projects' => '所有Web项目',
                'color' => '#E34F26',
                'sort_order' => 1
            ],
            [
                'skill_id' => 'css3',
                'name' => 'CSS3',
                'description' => '现代CSS技术，包括Flexbox、Grid、动画等。',
                'icon' => 'fab fa-css3-alt',
                'category' => 'frontend',
                'level' => 'expert',
                'experience_years' => 4,
                'experience_months' => 0,
                'projects' => '所有Web项目',
                'color' => '#1572B6',
                'sort_order' => 2
            ],
            [
                'skill_id' => 'javascript',
                'name' => 'JavaScript',
                'description' => '现代JavaScript开发，ES6+语法、异步编程等。',
                'icon' => 'fab fa-js-square',
                'category' => 'frontend',
                'level' => 'advanced',
                'experience_years' => 3,
                'experience_months' => 6,
                'projects' => '博客系统,数据可视化',
                'color' => '#F7DF1E',
                'sort_order' => 3
            ],
            [
                'skill_id' => 'php',
                'name' => 'PHP',
                'description' => '服务器端脚本语言，Web开发首选。',
                'icon' => 'fab fa-php',
                'category' => 'backend',
                'level' => 'advanced',
                'experience_years' => 3,
                'experience_months' => 6,
                'projects' => 'WordPress主题,电商系统',
                'color' => '#777BB4',
                'sort_order' => 4
            ],
            [
                'skill_id' => 'mysql',
                'name' => 'MySQL',
                'description' => '流行的开源关系型数据库。',
                'icon' => 'fas fa-database',
                'category' => 'database',
                'level' => 'advanced',
                'experience_years' => 3,
                'experience_months' => 0,
                'projects' => '电商平台,博客系统',
                'color' => '#4479A1',
                'sort_order' => 5
            ],
            [
                'skill_id' => 'git',
                'name' => 'Git',
                'description' => '分布式版本控制系统。',
                'icon' => 'fab fa-git-alt',
                'category' => 'tools',
                'level' => 'advanced',
                'experience_years' => 3,
                'experience_months' => 6,
                'projects' => '所有项目',
                'color' => '#F05032',
                'sort_order' => 6
            ]
        ];
        
        foreach ($default_skills as $skill) {
            $wpdb->insert($table_name, $skill);
        }
    }
}

// 初始化技能管理系统
$skills_management = new SkillsManagement();

// 提供全局函数供模板调用
if (!function_exists('get_skills_data')) {
    function get_skills_data() {
        return SkillsManagement::get_skills_data();
    }
}