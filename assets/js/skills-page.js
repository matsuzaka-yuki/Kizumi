/**
 * 技能展示页面 JavaScript
 * 基于mizuki主题设计的交互效果
 */

class SkillsPage {
    constructor() {
        this.skillsData = [];
        this.animationObserver = null;
        this.init();
    }

    /**
     * 初始化
     */
    init() {
        this.loadSkillsData();
        this.setupIntersectionObserver();
        this.bindEvents();
    }

    /**
     * 加载技能数据
     */
    loadSkillsData() {
        try {
            const dataElement = document.getElementById('skills-data');
            if (dataElement) {
                this.skillsData = JSON.parse(dataElement.textContent);
                this.renderSkills();
            } else {
                this.showError('技能数据加载失败');
            }
        } catch (error) {
            console.error('技能数据解析错误:', error);
            this.showError('技能数据格式错误');
        }
    }

    /**
     * 渲染技能内容
     */
    renderSkills() {
        this.renderStats();
        this.renderAdvancedSkills();
        this.renderSkillsByCategory();
        this.renderDistribution();
        this.startAnimations();
    }

    /**
     * 渲染统计信息
     */
    renderStats() {
        const stats = this.calculateStats();
        
        // 使用动画计数效果
        this.animateCounter('total-skills', stats.total);
        this.animateCounter('expert-skills', stats.byLevel.expert);
        this.animateCounter('advanced-skills', stats.byLevel.advanced);
        this.animateCounter('total-experience', stats.totalExperience.years);
    }

    /**
     * 计算统计信息
     */
    calculateStats() {
        const total = this.skillsData.length;
        const byLevel = {
            beginner: this.skillsData.filter(s => s.level === 'beginner').length,
            intermediate: this.skillsData.filter(s => s.level === 'intermediate').length,
            advanced: this.skillsData.filter(s => s.level === 'advanced').length,
            expert: this.skillsData.filter(s => s.level === 'expert').length
        };
        const byCategory = {
            frontend: this.skillsData.filter(s => s.category === 'frontend').length,
            backend: this.skillsData.filter(s => s.category === 'backend').length,
            database: this.skillsData.filter(s => s.category === 'database').length,
            tools: this.skillsData.filter(s => s.category === 'tools').length,
            other: this.skillsData.filter(s => s.category === 'other').length
        };

        // 计算总经验
        const totalMonths = this.skillsData.reduce((total, skill) => {
            return total + skill.experience.years * 12 + skill.experience.months;
        }, 0);
        const totalExperience = {
            years: Math.floor(totalMonths / 12),
            months: totalMonths % 12
        };

        return { total, byLevel, byCategory, totalExperience };
    }

    /**
     * 渲染高级技能
     */
    renderAdvancedSkills() {
        const advancedSkills = this.skillsData.filter(
            skill => skill.level === 'advanced' || skill.level === 'expert'
        );

        const container = document.getElementById('advanced-skills-container');
        if (!container) return;

        container.innerHTML = advancedSkills.map(skill => this.createAdvancedSkillCard(skill)).join('');
    }

    /**
     * 创建高级技能卡片
     */
    createAdvancedSkillCard(skill) {
        const levelWidth = this.getLevelWidth(skill.level);
        const levelClass = `skill-level-${skill.level}`;
        const experienceText = this.formatExperience(skill.experience);
        const certifications = skill.certifications || [];

        return `
            <div class="advanced-skill-card" data-skill="${skill.id}" style="--skill-color: ${skill.color || '#3b82f6'}">
                <div class="advanced-skill-header">
                    <div class="skill-icon-container">
                        <i class="${skill.icon}" style="color: ${skill.color || '#3b82f6'}"></i>
                    </div>
                    <div class="skill-info">
                        <div class="skill-header-top">
                            <h3 class="skill-name">${skill.name}</h3>
                            <span class="skill-level-badge ${levelClass}">
                                ${this.getLevelText(skill.level)}
                            </span>
                        </div>
                        <p class="skill-description">${skill.description}</p>
                        <div class="skill-experience-section">
                            <div class="skill-experience-header">
                                <span class="skill-experience-label">经验</span>
                                <span class="skill-experience-value">${experienceText}</span>
                            </div>
                            <div class="skill-progress-bar">
                                <div class="skill-progress-fill" data-width="${levelWidth}"></div>
                            </div>
                        </div>
                        ${certifications.length > 0 ? `
                            <div class="skill-certifications">
                                ${certifications.map(cert => `
                                    <span class="certification-badge">${cert}</span>
                                `).join('')}
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * 按分类渲染技能
     */
    renderSkillsByCategory() {
        const categories = ['frontend', 'backend', 'database', 'tools', 'other'];
        
        categories.forEach(category => {
            const skills = this.skillsData.filter(skill => skill.category === category);
            const container = document.getElementById(`${category}-skills`);
            const countElement = document.getElementById(`${category}-count`);
            
            if (container) {
                container.innerHTML = skills.map(skill => this.createSkillCard(skill)).join('');
            }
            
            if (countElement) {
                countElement.textContent = `(${skills.length})`;
            }
        });
    }

    /**
     * 创建技能卡片
     */
    createSkillCard(skill) {
        const levelWidth = this.getLevelWidth(skill.level);
        const levelClass = `skill-level-${skill.level}`;
        const experienceText = this.formatExperience(skill.experience);
        const projectsCount = skill.projects ? skill.projects.length : 0;

        return `
            <div class="skill-card" data-skill="${skill.id}" style="--skill-color: ${skill.color || '#3b82f6'}">
                <div class="skill-card-header">
                    <div class="skill-icon-container">
                        <i class="${skill.icon}" style="color: ${skill.color || '#3b82f6'}"></i>
                    </div>
                    <div class="skill-info">
                        <div class="skill-header-top">
                            <h3 class="skill-name">${skill.name}</h3>
                            <span class="skill-level-badge ${levelClass}">
                                ${this.getLevelText(skill.level)}
                            </span>
                        </div>
                        <p class="skill-description">${skill.description}</p>
                        <div class="skill-experience-section">
                            <div class="skill-experience-header">
                                <span class="skill-experience-label">经验</span>
                                <span class="skill-experience-value">${experienceText}</span>
                            </div>
                            <div class="skill-progress-bar">
                                <div class="skill-progress-fill" data-width="${levelWidth}"></div>
                            </div>
                        </div>
                        ${projectsCount > 0 ? `
                            <div class="skill-projects-info">
                                相关项目: ${projectsCount} 个
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * 渲染分布图表
     */
    renderDistribution() {
        const stats = this.calculateStats();
        this.renderLevelDistribution(stats);
        this.renderCategoryDistribution(stats);
    }

    /**
     * 渲染等级分布
     */
    renderLevelDistribution(stats) {
        const container = document.getElementById('level-distribution');
        if (!container) return;

        const levels = [
            { key: 'expert', label: '专家级', color: 'level-expert' },
            { key: 'advanced', label: '高级', color: 'level-advanced' },
            { key: 'intermediate', label: '中级', color: 'level-intermediate' },
            { key: 'beginner', label: '初级', color: 'level-beginner' }
        ];

        container.innerHTML = levels.map(level => {
            const count = stats.byLevel[level.key];
            const percentage = Math.round((count / stats.total) * 100);
            
            return `
                <div class="distribution-bar">
                    <div class="distribution-label">${level.label}</div>
                    <div class="distribution-progress">
                        <div class="distribution-fill ${level.color}" data-width="${percentage}%"></div>
                    </div>
                    <div class="distribution-count">${count}</div>
                </div>
            `;
        }).join('');
    }

    /**
     * 渲染分类分布
     */
    renderCategoryDistribution(stats) {
        const container = document.getElementById('category-distribution');
        if (!container) return;

        const categories = [
            { key: 'frontend', label: '前端', color: 'category-frontend' },
            { key: 'backend', label: '后端', color: 'category-backend' },
            { key: 'database', label: '数据库', color: 'category-database' },
            { key: 'tools', label: '工具', color: 'category-tools' },
            { key: 'other', label: '其他', color: 'category-other' }
        ];

        container.innerHTML = categories.map(category => {
            const count = stats.byCategory[category.key];
            const percentage = Math.round((count / stats.total) * 100);
            
            return `
                <div class="distribution-bar">
                    <div class="distribution-label">${category.label}</div>
                    <div class="distribution-progress">
                        <div class="distribution-fill ${category.color}" data-width="${percentage}%"></div>
                    </div>
                    <div class="distribution-count">${count}</div>
                </div>
            `;
        }).join('');
    }

    /**
     * 获取技能等级宽度
     */
    getLevelWidth(level) {
        const widths = {
            expert: '100%',
            advanced: '80%',
            intermediate: '60%',
            beginner: '40%'
        };
        return widths[level] || '20%';
    }

    /**
     * 获取等级文本
     */
    getLevelText(level) {
        const texts = {
            expert: '专家级',
            advanced: '高级',
            intermediate: '中级',
            beginner: '初级'
        };
        return texts[level] || '未知';
    }

    /**
     * 格式化经验时间
     */
    formatExperience(experience) {
        const { years, months } = experience;
        let text = '';
        
        if (years > 0) {
            text += `${years}年`;
        }
        
        if (months > 0) {
            text += `${months}个月`;
        }
        
        return text || '新手';
    }

    /**
     * 数字动画计数
     */
    animateCounter(elementId, targetValue, duration = 2000) {
        const element = document.getElementById(elementId);
        if (!element) return;

        let startValue = 0;
        const increment = targetValue / (duration / 16);
        
        const animate = () => {
            startValue += increment;
            if (startValue < targetValue) {
                element.textContent = Math.floor(startValue);
                requestAnimationFrame(animate);
            } else {
                element.textContent = targetValue;
            }
        };
        
        animate();
    }

    /**
     * 设置交叉观察器
     */
    setupIntersectionObserver() {
        const options = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        this.animationObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                    this.animateProgressBars(entry.target);
                }
            });
        }, options);
    }

    /**
     * 开始动画
     */
    startAnimations() {
        // 观察所有需要动画的元素
        const animatedElements = document.querySelectorAll(
            '.stat-card, .advanced-skill-card, .skill-card, .distribution-bar'
        );
        
        animatedElements.forEach((element, index) => {
            // 添加延迟动画类
            element.style.animationDelay = `${index * 0.1}s`;
            this.animationObserver.observe(element);
        });
    }

    /**
     * 动画进度条
     */
    animateProgressBars(container) {
        const progressBars = container.querySelectorAll('.skill-progress-fill, .distribution-fill');
        
        progressBars.forEach((bar, index) => {
            setTimeout(() => {
                const width = bar.dataset.width;
                if (width) {
                    bar.style.width = width;
                }
            }, index * 100);
        });
    }

    /**
     * 绑定事件
     */
    bindEvents() {
        // 技能卡片悬停效果
        document.addEventListener('mouseover', (e) => {
            const skillCard = e.target.closest('.skill-card, .advanced-skill-card');
            if (skillCard) {
                this.highlightSkillCard(skillCard);
            }
        });

        document.addEventListener('mouseout', (e) => {
            const skillCard = e.target.closest('.skill-card, .advanced-skill-card');
            if (skillCard) {
                this.unhighlightSkillCard(skillCard);
            }
        });

        // 响应式处理
        window.addEventListener('resize', this.debounce(() => {
            this.handleResize();
        }, 250));

        // 主题切换处理
        document.addEventListener('themechange', () => {
            this.handleThemeChange();
        });
    }

    /**
     * 高亮技能卡片
     */
    highlightSkillCard(card) {
        const progressFill = card.querySelector('.skill-progress-fill');
        if (progressFill) {
            progressFill.style.transform = 'scaleY(1.2)';
        }
    }

    /**
     * 取消高亮技能卡片
     */
    unhighlightSkillCard(card) {
        const progressFill = card.querySelector('.skill-progress-fill');
        if (progressFill) {
            progressFill.style.transform = 'scaleY(1)';
        }
    }

    /**
     * 处理窗口大小变化
     */
    handleResize() {
        // 重新计算布局
        this.recalculateLayout();
    }

    /**
     * 重新计算布局
     */
    recalculateLayout() {
        // 可以在这里添加响应式布局调整逻辑
        console.log('Layout recalculated');
    }

    /**
     * 处理主题变化
     */
    handleThemeChange() {
        // 重新应用主题相关的样式
        console.log('Theme changed');
    }

    /**
     * 显示错误信息
     */
    showError(message) {
        const container = document.querySelector('.skills-main-content .container');
        if (container) {
            container.innerHTML = `
                <div class="skills-error">
                    <div class="error-icon">⚠️</div>
                    <div class="error-message">${message}</div>
                    <div class="error-description">请检查技能数据配置或联系管理员</div>
                </div>
            `;
        }
    }

    /**
     * 防抖函数
     */
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
}

// 页面加载完成后初始化
document.addEventListener('DOMContentLoaded', () => {
    new SkillsPage();
});

// 添加CSS动画类
const style = document.createElement('style');
style.textContent = `
    .animate-in {
        animation: fadeInUp 0.6s ease-out forwards;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .skill-progress-fill,
    .distribution-fill {
        width: 0;
        transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .skill-progress-fill {
        transition: width 1s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s ease;
    }
`;
document.head.appendChild(style);