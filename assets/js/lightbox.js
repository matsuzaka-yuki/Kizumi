/**
 * Kizumi主题全局图片灯箱功能
 * 基于Fancybox 5.0
 */

(function($) {
    'use strict';
    
    // 等待DOM加载完成
    $(document).ready(function() {
        initKizumiLightbox();
    });
    
    // 初始化灯箱功能
    function initKizumiLightbox() {
        // 检查Fancybox是否已加载
        if (typeof Fancybox === 'undefined') {
            console.warn('Fancybox未加载，灯箱功能不可用');
            return;
        }
        
        // 配置Fancybox
        Fancybox.bind('[data-fancybox]', {
            // 基础配置
            animated: true,
            showClass: 'f-fadeIn',
            hideClass: 'f-fadeOut',
            
            // 工具栏配置
            Toolbar: {
                display: {
                    left: ['infobar'],
                    middle: [],
                    right: ['slideshow', 'zoom', 'fullscreen', 'close']
                }
            },
            
            // 缩略图配置
            Thumbs: {
                type: 'classic'
            },
            
            // 图片配置
            Images: {
                zoom: true,
                protected: true
            },
            
            // 界面配置
            UI: {
                theme: 'dark'
            },
            
            // 本地化
            l10n: {
                CLOSE: '关闭',
                NEXT: '下一张',
                PREV: '上一张',
                MODAL: '您可以使用键盘导航',
                ERROR: '加载失败，请稍后重试',
                IMAGE_ERROR: '图片加载失败',
                ELEMENT_NOT_FOUND: '未找到HTML元素',
                AJAX_NOT_FOUND: '加载失败: {{STATUS}}',
                AJAX_FORBIDDEN: '加载失败: {{STATUS}}',
                IFRAME_ERROR: '页面加载失败',
                TOGGLE_ZOOM: '切换缩放模式',
                TOGGLE_THUMBS: '切换缩略图',
                TOGGLE_SLIDESHOW: '切换幻灯片',
                TOGGLE_FULLSCREEN: '切换全屏模式',
                DOWNLOAD: '下载'
            }
        });
        
        // 自动为内容区域的图片添加灯箱功能
        autoBindContentImages();
        
        // 初始化日记和相册页面的灯箱功能
        initDiaryGalleryLightbox();
        
        // 监听动态内容变化
        observeContentChanges();
        
        console.log('Kizumi灯箱功能已初始化');
    }
    
    // 自动为内容中的图片添加灯箱功能
    function autoBindContentImages() {
        // 选择器：文章内容、页面内容等区域的图片
        const contentSelectors = [
            '.entry-content img',
            '.post-content img', 
            '.page-content img',
            '.content img',
            'article img'
        ];
        
        contentSelectors.forEach(selector => {
            $(selector).each(function() {
                const $img = $(this);
                const $parent = $img.parent();
                
                // 跳过已经在链接中的图片
                if ($parent.is('a')) {
                    return;
                }
                
                // 跳过特定类名的图片
                if ($img.hasClass('no-lightbox') || $img.hasClass('emoji')) {
                    return;
                }
                
                // 获取图片信息
                const imgSrc = $img.attr('src') || $img.attr('data-src');
                const imgAlt = $img.attr('alt') || '';
                const imgTitle = $img.attr('title') || imgAlt;
                
                if (imgSrc) {
                    // 包装图片为灯箱链接
                    $img.wrap(`<a href="${imgSrc}" data-fancybox="content-gallery" data-caption="${imgTitle}"></a>`);
                    
                    // 添加鼠标悬停效果
                    $img.addClass('lightbox-image');
                }
            });
        });
    }
    
    // 监听动态内容变化
    function observeContentChanges() {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length > 0) {
                    // 为新添加的图片添加灯箱
                    autoBindContentImages();
                    // 为日记和相册页面添加特殊处理
                    initDiaryGalleryLightbox();
                }
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
    
    // 初始化全站图片灯箱功能
      function initDiaryGalleryLightbox() {
          console.log('初始化全站图片灯箱功能');
          
          // 全站图片灯箱处理
          var allImages = $('img').not('.no-lightbox, .avatar, .emoji, .wp-smiley, .logo');
          console.log('找到图片总数:', allImages.length);
          
          allImages.each(function(index) {
              var $img = $(this);
              var imgSrc = $img.attr('src');
              
              // 跳过已经被链接包裹的图片（除非是相册封面）
              if ($img.parent('a').length > 0 && !$img.closest('.gallery-card-image').length) {
                  var $existingLink = $img.parent('a');
                  // 如果现有链接指向图片，则添加灯箱属性
                  var linkHref = $existingLink.attr('href');
                  if (linkHref && (linkHref.match(/\.(jpg|jpeg|png|gif|webp|svg)$/i) || linkHref === imgSrc)) {
                      $existingLink.attr('data-fancybox', 'all-images');
                      $existingLink.attr('data-caption', $img.attr('alt') || '');
                  }
                  return;
              }
              
              // 为相册封面特殊处理
              if ($img.closest('.gallery-card-image').length) {
                  var $link = $img.closest('a');
                  if ($link.length && imgSrc) {
                      $link.attr('data-fancybox', 'gallery-covers');
                      $link.attr('data-src', imgSrc);
                      $link.attr('data-caption', $img.attr('alt') || '');
                      
                      $link.on('click', function(e) {
                          e.preventDefault();
                          Fancybox.show([{
                              src: imgSrc,
                              type: 'image',
                              caption: $img.attr('alt') || ''
                          }]);
                      });
                  }
                  return;
              }
              
              // 为其他图片创建灯箱链接
              if (imgSrc && !$img.hasClass('no-lightbox')) {
                  var $link = $('<a>');
                  $link.attr('href', imgSrc);
                  $link.attr('data-fancybox', 'all-images');
                  $link.attr('data-caption', $img.attr('alt') || '');
                  $img.wrap($link);
              }
          });
          
          console.log('全站灯箱初始化完成');
      }
    
    // 添加键盘快捷键支持
    $(document).on('keydown', function(e) {
        // ESC键关闭灯箱
        if (e.keyCode === 27 && Fancybox.getInstance()) {
            Fancybox.close();
        }
    });
    
    // 添加触摸手势支持（移动端）
    if ('ontouchstart' in window) {
        let touchStartX = 0;
        let touchStartY = 0;
        
        $(document).on('touchstart', '.fancybox__container', function(e) {
            touchStartX = e.originalEvent.touches[0].clientX;
            touchStartY = e.originalEvent.touches[0].clientY;
        });
        
        $(document).on('touchend', '.fancybox__container', function(e) {
            const touchEndX = e.originalEvent.changedTouches[0].clientX;
            const touchEndY = e.originalEvent.changedTouches[0].clientY;
            const deltaX = touchEndX - touchStartX;
            const deltaY = touchEndY - touchStartY;
            
            // 水平滑动切换图片
            if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 50) {
                const instance = Fancybox.getInstance();
                if (instance) {
                    if (deltaX > 0) {
                        instance.prev();
                    } else {
                        instance.next();
                    }
                }
            }
        });
    }
    
    // 全局方法：手动打开灯箱
    window.kizumiLightbox = {
        open: function(src, options = {}) {
            Fancybox.show([{
                src: src,
                type: 'image',
                caption: options.caption || ''
            }], options);
        },
        
        openGallery: function(images, options = {}) {
            const items = images.map(img => ({
                src: typeof img === 'string' ? img : img.src,
                type: 'image',
                caption: typeof img === 'string' ? '' : (img.caption || '')
            }));
            
            Fancybox.show(items, options);
        }
    };
    
})(jQuery);