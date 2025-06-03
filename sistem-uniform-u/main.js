// ===== UTILITY FUNCTIONS =====
const debounce = (func, wait) => {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
};

const throttle = (func, limit) => {
  let inThrottle;
  return function() {
    const args = arguments;
    const context = this;
    if (!inThrottle) {
      func.apply(context, args);
      inThrottle = true;
      setTimeout(() => inThrottle = false, limit);
    }
  }
};

// ===== HEADER SCROLL EFFECT =====
class HeaderController {
  constructor() {
    this.header = document.getElementById('header');
    this.lastScrollTop = 0;
    this.init();
  }

  init() {
    window.addEventListener('scroll', throttle(() => {
      this.handleScroll();
    }, 16));
  }

  handleScroll() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    
    // Add scrolled class for background effect
    if (scrollTop > 50) {
      this.header.classList.add('scrolled');
    } else {
      this.header.classList.remove('scrolled');
    }

    this.lastScrollTop = scrollTop;
  }
}

// ===== SMOOTH SCROLLING FOR NAVIGATION =====
class SmoothScrolling {
  constructor() {
    this.init();
  }

  init() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', (e) => {
        e.preventDefault();
        const target = document.querySelector(anchor.getAttribute('href'));
        
        if (target) {
          const headerHeight = document.getElementById('header').offsetHeight;
          const targetPosition = target.offsetTop - headerHeight - 20;
          
          window.scrollTo({
            top: targetPosition,
            behavior: 'smooth'
          });
        }
      });
    });
  }
}

// ===== INTERSECTION OBSERVER FOR ANIMATIONS =====
class AnimationController {
  constructor() {
    this.observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };
    this.init();
  }

  init() {
    this.observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          this.triggerAnimation(entry.target);
        }
      });
    }, this.observerOptions);

    // Observe all elements with animation classes
    const animatedElements = document.querySelectorAll('.fade-in, .slide-in-left, .slide-in-right');
    animatedElements.forEach(el => {
      // Reset animation state
      el.style.opacity = '0';
      el.style.transform = this.getInitialTransform(el);
      this.observer.observe(el);
    });
  }

  getInitialTransform(element) {
    if (element.classList.contains('slide-in-left')) {
      return 'translateX(-50px)';
    } else if (element.classList.contains('slide-in-right')) {
      return 'translateX(50px)';
    } else if (element.classList.contains('fade-in')) {
      return 'translateY(30px)';
    }
    return 'none';
  }

  triggerAnimation(element) {
    // Add staggered delay for grid items
    const delay = this.calculateDelay(element);
    
    setTimeout(() => {
      element.style.opacity = '1';
      element.style.transform = 'translate(0)';
      element.style.transition = 'all 0.8s cubic-bezier(0.16, 1, 0.3, 1)';
    }, delay);

    this.observer.unobserve(element);
  }

  calculateDelay(element) {
    // Add staggered animation for grid items
    if (element.closest('.categories-grid, .features-grid')) {
      const siblings = Array.from(element.parentNode.children);
      const index = siblings.indexOf(element);
      return index * 100; // 100ms delay between each item
    }
    return 0;
  }
}

// ===== MOBILE MENU CONTROLLER =====
class MobileMenuController {
  constructor() {
    this.toggle = document.querySelector('.mobile-menu-toggle');
    this.menu = document.querySelector('.nav-menu');
    this.hamburgerLines = document.querySelectorAll('.hamburger');
    this.isOpen = false;
    this.init();
  }

  init() {
    if (this.toggle) {
      this.toggle.addEventListener('click', () => {
        this.toggleMenu();
      });

      // Close menu when clicking on nav links
      document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
          if (this.isOpen) {
            this.toggleMenu();
          }
        });
      });

      // Close menu when clicking outside
      document.addEventListener('click', (e) => {
        if (this.isOpen && !e.target.closest('.nav-container')) {
          this.toggleMenu();
        }
      });
    }
  }

  toggleMenu() {
    this.isOpen = !this.isOpen;
    
    if (this.isOpen) {
      this.openMenu();
    } else {
      this.closeMenu();
    }
  }

  openMenu() {
    // Transform hamburger to X
    this.hamburgerLines[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
    this.hamburgerLines[1].style.opacity = '0';
    this.hamburgerLines[2].style.transform = 'rotate(-45deg) translate(7px, -6px)';
    
    // Show menu
    this.menu.style.display = 'flex';
    this.menu.style.flexDirection = 'column';
    this.menu.style.position = 'absolute';
    this.menu.style.top = '100%';
    this.menu.style.left = '0';
    this.menu.style.right = '0';
    this.menu.style.background = 'rgba(255, 255, 255, 0.98)';
    this.menu.style.backdropFilter = 'blur(20px)';
    this.menu.style.padding = '2rem';
    this.menu.style.boxShadow = '0 10px 25px rgba(0, 0, 0, 0.1)';
    this.menu.style.border = '1px solid var(--border-light)';
    this.menu.style.borderTop = 'none';
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
  }

  closeMenu() {
    // Reset hamburger
    this.hamburgerLines.forEach(line => {
      line.style.transform = 'none';
      line.style.opacity = '1';
    });
    
    // Hide menu
    this.menu.style.display = '';
    this.menu.style.flexDirection = '';
    this.menu.style.position = '';
    this.menu.style.top = '';
    this.menu.style.left = '';
    this.menu.style.right = '';
    this.menu.style.background = '';
    this.menu.style.backdropFilter = '';
    this.menu.style.padding = '';
    this.menu.style.boxShadow = '';
    this.menu.style.border = '';
    
    // Restore body scroll
    document.body.style.overflow = '';
  }
}

// ===== CARD HOVER EFFECTS =====
class CardEffects {
  constructor() {
    this.init();
  }

  init() {
    const cards = document.querySelectorAll('.category-card, .feature-card');
    
    cards.forEach(card => {
      card.addEventListener('mouseenter', (e) => {
        this.onCardHover(e.target);
      });
      
      card.addEventListener('mouseleave', (e) => {
        this.onCardLeave(e.target);
      });
    });
  }

  onCardHover(card) {
    // Add subtle scale and glow effect
    card.style.transform = 'translateY(-8px) scale(1.02)';
    card.style.boxShadow = '0 25px 50px -12px rgba(139, 21, 56, 0.15)';
    card.style.transition = 'all 0.3s cubic-bezier(0.16, 1, 0.3, 1)';
  }

  onCardLeave(card) {
    card.style.transform = '';
    card.style.boxShadow = '';
  }
}

// ===== FLOATING ANIMATION CONTROLLER =====
class FloatingAnimations {
  constructor() {
    this.init();
  }

  init() {
    const floatingCards = document.querySelectorAll('.floating-card');
    
    floatingCards.forEach((card, index) => {
      this.animateFloatingCard(card, index);
    });
  }

  animateFloatingCard(card, index) {
    const delay = index * 1500;
    const duration = 3000 + (index * 500);
    
    card.style.animationDelay = `${delay}ms`;
    card.style.animationDuration = `${duration}ms`;
    
    // Add mouse interaction
    card.addEventListener('mouseenter', () => {
      card.style.animationPlayState = 'paused';
      card.style.transform = 'translateY(-15px) scale(1.05)';
    });
    
    card.addEventListener('mouseleave', () => {
      card.style.animationPlayState = 'running';
      card.style.transform = '';
    });
  }
}

// ===== COUNTER ANIMATION =====
class CounterAnimation {
  constructor() {
    this.counters = document.querySelectorAll('.stat-number');
    this.init();
  }

  init() {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          this.animateCounter(entry.target);
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.5 });

    this.counters.forEach(counter => {
      observer.observe(counter);
    });
  }

  animateCounter(counter) {
    const target = counter.textContent;
    const isPercentage = target.includes('%');
    const numericValue = parseFloat(target.replace(/[^\d.]/g, ''));
    const suffix = target.replace(/[\d.]/g, '');
    
    let current = 0;
    const increment = numericValue / 60; // 60 frames for 1 second animation
    const timer = setInterval(() => {
      current += increment;
      if (current >= numericValue) {
        current = numericValue;
        clearInterval(timer);
      }
      
      if (isPercentage) {
        counter.textContent = current.toFixed(1) + '%';
      } else if (target.includes('K')) {
        counter.textContent = Math.floor(current) + 'K+';
      } else {
        counter.textContent = Math.floor(current) + '+';
      }
    }, 16);
  }
}

// ===== PARALLAX SCROLLING =====
class ParallaxController {
  constructor() {
    this.elements = document.querySelectorAll('.hero-visual, .floating-card');
    this.init();
  }

  init() {
    window.addEventListener('scroll', throttle(() => {
      this.updateParallax();
    }, 16));
  }

  updateParallax() {
    const scrolled = window.pageYOffset;
    const rate = scrolled * -0.5;
    
    this.elements.forEach((element, index) => {
      const speed = 0.5 + (index * 0.1);
      const yPos = -(scrolled * speed);
      element.style.transform = `translateY(${yPos}px)`;
    });
  }
}

// ===== LOADING ANIMATION =====
class LoadingController {
  constructor() {
    this.init();
  }

  init() {
    window.addEventListener('load', () => {
      this.hideLoader();
      this.startInitialAnimations();
    });
  }

  hideLoader() {
    const loader = document.querySelector('.loader');
    if (loader) {
      loader.style.opacity = '0';
      setTimeout(() => {
        loader.style.display = 'none';
      }, 300);
    }
  }

  startInitialAnimations() {
    // Trigger hero animations
    const heroElements = document.querySelectorAll('.hero-text, .hero-visual');
    heroElements.forEach((element, index) => {
      setTimeout(() => {
        element.style.opacity = '1';
        element.style.transform = 'translateY(0)';
      }, index * 200);
    });
  }
}

// ===== FORM VALIDATION =====
class FormValidator {
  constructor() {
    this.forms = document.querySelectorAll('form');
    this.init();
  }

  init() {
    this.forms.forEach(form => {
      form.addEventListener('submit', (e) => {
        if (!this.validateForm(form)) {
          e.preventDefault();
        }
      });

      // Real-time validation
      const inputs = form.querySelectorAll('input, textarea');
      inputs.forEach(input => {
        input.addEventListener('blur', () => {
          this.validateField(input);
        });
      });
    });
  }

  validateForm(form) {
    const inputs = form.querySelectorAll('input[required], textarea[required]');
    let isValid = true;

    inputs.forEach(input => {
      if (!this.validateField(input)) {
        isValid = false;
      }
    });

    return isValid;
  }

  validateField(field) {
    const value = field.value.trim();
    const type = field.type;
    let isValid = true;
    let message = '';

    // Check if required field is empty
    if (field.hasAttribute('required') && !value) {
      isValid = false;
      message = 'Field ini wajib diisi';
    }
    // Email validation
    else if (type === 'email' && value) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(value)) {
        isValid = false;
        message = 'Format email tidak valid';
      }
    }
    // Phone validation
    else if (field.name === 'phone' && value) {
      const phoneRegex = /^[0-9+\-\s()]+$/;
      if (!phoneRegex.test(value) || value.length < 10) {
        isValid = false;
        message = 'Format nomor telepon tidak valid';
      }
    }

    this.showValidationMessage(field, isValid, message);
    return isValid;
  }

  showValidationMessage(field, isValid, message) {
    // Remove existing message
    const existingMessage = field.parentNode.querySelector('.validation-message');
    if (existingMessage) {
      existingMessage.remove();
    }

    // Add validation styling
    if (isValid) {
      field.classList.remove('invalid');
      field.classList.add('valid');
    } else {
      field.classList.remove('valid');
      field.classList.add('invalid');
      
      // Add error message
      const messageElement = document.createElement('div');
      messageElement.className = 'validation-message';
      messageElement.textContent = message;
      messageElement.style.color = 'var(--accent)';
      messageElement.style.fontSize = '0.875rem';
      messageElement.style.marginTop = '0.5rem';
      field.parentNode.appendChild(messageElement);
    }
  }
}

// ===== SCROLL PROGRESS INDICATOR =====
class ScrollProgress {
  constructor() {
    this.createProgressBar();
    this.init();
  }

  createProgressBar() {
    const progressBar = document.createElement('div');
    progressBar.className = 'scroll-progress';
    progressBar.style.cssText = `
      position: fixed;
      top: 0;
      left: 0;
      width: 0%;
      height: 3px;
      background: var(--gradient-primary);
      z-index: 9999;
      transition: width 0.1s ease-out;
    `;
    document.body.appendChild(progressBar);
    this.progressBar = progressBar;
  }

  init() {
    window.addEventListener('scroll', throttle(() => {
      this.updateProgress();
    }, 16));
  }

  updateProgress() {
    const scrollTop = window.pageYOffset;
    const docHeight = document.documentElement.scrollHeight - window.innerHeight;
    const scrollPercent = (scrollTop / docHeight) * 100;
    
    this.progressBar.style.width = scrollPercent + '%';
  }
}

// ===== PERFORMANCE OPTIMIZATION =====
class PerformanceOptimizer {
  constructor() {
    this.init();
  }

  init() {
    // Lazy load images
    this.lazyLoadImages();
    
    // Preload critical resources
    this.preloadResources();
    
    // Optimize scroll performance
    this.optimizeScrollPerformance();
  }

  lazyLoadImages() {
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const img = entry.target;
          img.src = img.dataset.src;
          img.removeAttribute('data-src');
          imageObserver.unobserve(img);
        }
      });
    });

    images.forEach(img => imageObserver.observe(img));
  }

  preloadResources() {
    // Preload critical CSS
    const criticalCSS = document.createElement('link');
    criticalCSS.rel = 'preload';
    criticalCSS.as = 'style';
    criticalCSS.href = 'assets/css/style.css';
    document.head.appendChild(criticalCSS);
  }

  optimizeScrollPerformance() {
    // Use passive event listeners for better scroll performance
    let ticking = false;
    
    function updateScrollEffects() {
      // Batch all scroll-related DOM updates
      requestAnimationFrame(() => {
        ticking = false;
      });
    }
    
    window.addEventListener('scroll', () => {
      if (!ticking) {
        requestAnimationFrame(updateScrollEffects);
        ticking = true;
      }
    }, { passive: true });
  }
}

// ===== MAIN APPLICATION =====
class UniformUApp {
  constructor() {
    this.init();
  }

  init() {
    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', () => {
        this.initializeComponents();
      });
    } else {
      this.initializeComponents();
    }
  }

  initializeComponents() {
    // Initialize all components
    new HeaderController();
    new SmoothScrolling();
    new AnimationController();
    new MobileMenuController();
    new CardEffects();
    new FloatingAnimations();
    new CounterAnimation();
    new ParallaxController();
    new LoadingController();
    new FormValidator();
    new ScrollProgress();
    new PerformanceOptimizer();

    // Add custom event listeners
    this.addCustomEventListeners();
  }

  addCustomEventListeners() {
    // Handle button clicks with analytics
    document.querySelectorAll('.btn-primary, .btn-secondary').forEach(btn => {
      btn.addEventListener('click', (e) => {
        this.trackButtonClick(e.target);
      });
    });

    // Handle form submissions
    document.querySelectorAll('form').forEach(form => {
      form.addEventListener('submit', (e) => {
        this.handleFormSubmission(e);
      });
    });
  }

  trackButtonClick(button) {
    const buttonText = button.textContent.trim();
    const buttonType = button.classList.contains('btn-primary') ? 'primary' : 'secondary';
    
    // Analytics tracking would go here
    console.log(`Button clicked: ${buttonText} (${buttonType})`);
  }

  handleFormSubmission(event) {
    const form = event.target;
    const formData = new FormData(form);
    
    // Show loading state
    const submitButton = form.querySelector('[type="submit"]');
    if (submitButton) {
      submitButton.textContent = 'Mengirim...';
      submitButton.disabled = true;
    }
    
    // Form submission logic would go here
    console.log('Form submitted:', Object.fromEntries(formData));
  }
}

// ===== INITIALIZE APPLICATION =====
const app = new UniformUApp();