<?php
   /**
    * The template for displaying the footer
    *
    * Contains the closing of the #content div and all content after.
    *
    * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
    *
    * @package upwork
    */

   ?>
<section class="authorization">
   <a href="javascript:void(0);" class="authorization__close">Close <span><img src="<?php echo get_template_directory_uri(); ?>/img/close.svg" alt="close"></span></a>
   <div class="authorization__box authorization__box_login">
      <div class="authorization__header">
         <div class="authorization__title">
            My Account
         </div>
         <div class="authorization__lincks">
            <a href="javascript:void(0);" class="authorization__linck authorization__linck_login authorization__linck_active">Log in</a>
            <a href="javascript:void(0);" class="authorization__linck authorization__linck_register">Register</a>
         </div>
      </div>
      <div class="authorization__body active">
         <form action="" class="login_form">
            <label for="user_email">
            <span>Email</span>
            <input type="text" placeholder="Email" id="user_email">
            </label>
            <label for="user_pass">
            <span>Password</span>
            <input type="password" placeholder="Password" id="user_pass">
            </label>
            <a href="javascript:void(0);" class="authorization__forgot">
            Forgot your password?
            </a>
            <button class="authorization__button" id="login_user">
              Log In <img src="<?php echo get_template_directory_uri(); ?>/img/arrow-right.svg" alt="arrow">
              <span class="authorization__button-loader">
                <svg class="next_step_svg" style="/* display: none; */" xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0" width="80px" height="69px" viewBox="0 0 128 35" xml:space="preserve"><rect x="0" y="0" width="100%" height="100%" fill="transparent"></rect>
                                <g>
                                    <circle fill="#3f8d5b" cx="17.5" cy="17.5" r="17.5"></circle>
                                    <animate attributeName="opacity" dur="2700ms" begin="0s" repeatCount="indefinite" keyTimes="0;0.167;0.5;0.668;1" values="0.3;1;1;0.3;0.3"></animate>
                                </g>
                                <g>
                                    <circle fill="#3f8d5b" cx="110.5" cy="17.5" r="17.5"></circle>
                                    <animate attributeName="opacity" dur="2700ms" begin="0s" repeatCount="indefinite" keyTimes="0;0.334;0.5;0.835;1" values="0.3;0.3;1;1;0.3"></animate>
                                </g>
                                <g>
                                    <circle fill="#3f8d5b" cx="64" cy="17.5" r="17.5"></circle>
                                    <animate attributeName="opacity" dur="2700ms" begin="0s" repeatCount="indefinite" keyTimes="0;0.167;0.334;0.668;0.835;1" values="0.3;0.3;1;1;0.3;0.3"></animate>
                                </g>
                </svg>
              </span>
            </button>
         </form>
      </div>
      <div class="authorization__body">
         <form action="" id="reg_form">
            <div class="authorization__union-block">
               <label for="first_name" class="form-group">
               <span>First Name *</span>
               <input type="text" placeholder="First Name" id="first_name" required>
               </label>
               <label for="last_name" class="form-group">
               <span>Last Name *</span>
               <input type="text" placeholder="Last Name" id="last_name" required>
               </label>
            </div>
            <label for="email" class="form-group">
            <span>Email</span>
            <input type="email" placeholder="Email" id="email" name="email" required>
            </label>
            <div class="authorization__union-block">
               <label for="password" class="form-group error-info">
               <span>Password</span>
               <input type="password" placeholder="Password" id="password" data-pristine-pattern-message="Minimum 8 characters, at least one uppercase letter, one lowercase letter and one number"
                  data-pristine-pattern= "/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{8,}$/" name="password" required>
               </label>
               <label for="confirm_password" class="form-group">
               <span>Confirm Password *</span>
               <input type="password" placeholder="Confirm Password" name="confirm_password" id="confirm_password" required>
               </label>
            </div>
            <button class="authorization__button" id="register_submit">
              Create account <img src="<?php echo get_template_directory_uri(); ?>/img/arrow-right.svg" alt="arrow">
              <span class="authorization__button-loader">
                <svg class="next_step_svg" style="/* display: none; */" xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0" width="80px" height="69px" viewBox="0 0 128 35" xml:space="preserve"><rect x="0" y="0" width="100%" height="100%" fill="transparent"></rect>
                                <g>
                                    <circle fill="#3f8d5b" cx="17.5" cy="17.5" r="17.5"></circle>
                                    <animate attributeName="opacity" dur="2700ms" begin="0s" repeatCount="indefinite" keyTimes="0;0.167;0.5;0.668;1" values="0.3;1;1;0.3;0.3"></animate>
                                </g>
                                <g>
                                    <circle fill="#3f8d5b" cx="110.5" cy="17.5" r="17.5"></circle>
                                    <animate attributeName="opacity" dur="2700ms" begin="0s" repeatCount="indefinite" keyTimes="0;0.334;0.5;0.835;1" values="0.3;0.3;1;1;0.3"></animate>
                                </g>
                                <g>
                                    <circle fill="#3f8d5b" cx="64" cy="17.5" r="17.5"></circle>
                                    <animate attributeName="opacity" dur="2700ms" begin="0s" repeatCount="indefinite" keyTimes="0;0.167;0.334;0.668;0.835;1" values="0.3;0.3;1;1;0.3;0.3"></animate>
                                </g>
                </svg>
              </span>
            </button>
         </form>
      </div>
      <div class="authorization__body">
         <form action="" class="forgot_pass">
            <label for="user_email1">
            <span>Email</span>
            <input type="text" placeholder="Email" id="user_email1">
            </label>
            <button class="authorization__button" id="send_pass">
              Send new password <img src="<?php echo get_template_directory_uri(); ?>/img/arrow-right.svg" alt="arrow">
              <span class="authorization__button-loader">
                <svg class="next_step_svg" style="/* display: none; */" xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0" width="80px" height="69px" viewBox="0 0 128 35" xml:space="preserve"><rect x="0" y="0" width="100%" height="100%" fill="transparent"></rect>
                                <g>
                                    <circle fill="#3f8d5b" cx="17.5" cy="17.5" r="17.5"></circle>
                                    <animate attributeName="opacity" dur="2700ms" begin="0s" repeatCount="indefinite" keyTimes="0;0.167;0.5;0.668;1" values="0.3;1;1;0.3;0.3"></animate>
                                </g>
                                <g>
                                    <circle fill="#3f8d5b" cx="110.5" cy="17.5" r="17.5"></circle>
                                    <animate attributeName="opacity" dur="2700ms" begin="0s" repeatCount="indefinite" keyTimes="0;0.334;0.5;0.835;1" values="0.3;0.3;1;1;0.3"></animate>
                                </g>
                                <g>
                                    <circle fill="#3f8d5b" cx="64" cy="17.5" r="17.5"></circle>
                                    <animate attributeName="opacity" dur="2700ms" begin="0s" repeatCount="indefinite" keyTimes="0;0.167;0.334;0.668;0.835;1" values="0.3;0.3;1;1;0.3;0.3"></animate>
                                </g>
                </svg>
              </span>
            </button>
         </form>
      </div>
      <div class="authorization__footer">
         <div class="authorization__information">Already have an account ? <a
            href="./register.html">Login</a></div>
      </div>
   </div>
</section>
<div class="notification-error message">
    <span>Error</span>
   <svg class="notification__exit" width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M1.19497 0.205025C0.921608 -0.0683418 0.478392 -0.0683418 0.205025 0.205025C-0.0683417 0.478392 -0.0683417 0.921608 0.205025 1.19497L6.07408 7.06403L0.205025 12.9331C-0.0683415 13.2065 -0.0683415 13.6497 0.205025 13.923C0.478392 14.1964 0.921608 14.1964 1.19497 13.923L7.06403 8.05398L12.9329 13.9229C13.2063 14.1963 13.6495 14.1963 13.9229 13.9229C14.1963 13.6495 14.1963 13.2063 13.9229 12.9329L8.05398 7.06403L13.9229 1.19512C14.1963 0.921754 14.1963 0.478537 13.9229 0.20517C13.6495 -0.0681966 13.2063 -0.0681955 12.9329 0.205171L7.06403 6.07408L1.19497 0.205025Z" fill="#383838"/>
   </svg>
</div>
<div class="notification-inforrmation message">
    <span>Inforrmation</span>
   <svg class="notification__exit" width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M1.19497 0.205025C0.921608 -0.0683418 0.478392 -0.0683418 0.205025 0.205025C-0.0683417 0.478392 -0.0683417 0.921608 0.205025 1.19497L6.07408 7.06403L0.205025 12.9331C-0.0683415 13.2065 -0.0683415 13.6497 0.205025 13.923C0.478392 14.1964 0.921608 14.1964 1.19497 13.923L7.06403 8.05398L12.9329 13.9229C13.2063 14.1963 13.6495 14.1963 13.9229 13.9229C14.1963 13.6495 14.1963 13.2063 13.9229 12.9329L8.05398 7.06403L13.9229 1.19512C14.1963 0.921754 14.1963 0.478537 13.9229 0.20517C13.6495 -0.0681966 13.2063 -0.0681955 12.9329 0.205171L7.06403 6.07408L1.19497 0.205025Z" fill="#383838"/>
   </svg>
</div>
<div class="notification-caution message">
    <span>Caution</span>
   <svg class="notification__exit" width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M1.19497 0.205025C0.921608 -0.0683418 0.478392 -0.0683418 0.205025 0.205025C-0.0683417 0.478392 -0.0683417 0.921608 0.205025 1.19497L6.07408 7.06403L0.205025 12.9331C-0.0683415 13.2065 -0.0683415 13.6497 0.205025 13.923C0.478392 14.1964 0.921608 14.1964 1.19497 13.923L7.06403 8.05398L12.9329 13.9229C13.2063 14.1963 13.6495 14.1963 13.9229 13.9229C14.1963 13.6495 14.1963 13.2063 13.9229 12.9329L8.05398 7.06403L13.9229 1.19512C14.1963 0.921754 14.1963 0.478537 13.9229 0.20517C13.6495 -0.0681966 13.2063 -0.0681955 12.9329 0.205171L7.06403 6.07408L1.19497 0.205025Z" fill="#383838"/>
   </svg>
</div>
<div class="notification-successful message">
    <span>Successful</span>
   <svg class="notification__exit" width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M1.19497 0.205025C0.921608 -0.0683418 0.478392 -0.0683418 0.205025 0.205025C-0.0683417 0.478392 -0.0683417 0.921608 0.205025 1.19497L6.07408 7.06403L0.205025 12.9331C-0.0683415 13.2065 -0.0683415 13.6497 0.205025 13.923C0.478392 14.1964 0.921608 14.1964 1.19497 13.923L7.06403 8.05398L12.9329 13.9229C13.2063 14.1963 13.6495 14.1963 13.9229 13.9229C14.1963 13.6495 14.1963 13.2063 13.9229 12.9329L8.05398 7.06403L13.9229 1.19512C14.1963 0.921754 14.1963 0.478537 13.9229 0.20517C13.6495 -0.0681966 13.2063 -0.0681955 12.9329 0.205171L7.06403 6.07408L1.19497 0.205025Z" fill="#383838"/>
   </svg>
</div>
<a href="#" class="footer__button_up"><img src="<?php echo get_template_directory_uri(); ?>/img/arrow-up-button.svg" alt="arrow"></a>
<footer class="footer">
   <div class="container _container">
      <div class="footer__union footer__union_button">
         <a class="footer__logo" href="<?php echo get_home_url(); ?>">
         <?php $image = get_field('logotype_footer', 'option'); ?>
         <?php if($image){ ?>
         <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
         <?php } ?>
         </a>
         <?php wp_nav_menu([
            'menu'=>'Footer menu',
            'menu_class'=>'footer__items',
            'container'=>false,
            ]) ?>
      </div>
      <div class="footer__union">
         <div class="footer__social">
            <?php $link = get_field('instagram_link', 'option'); ?>
            <?php if($link){ ?>
            <a href="<?php echo $link; ?>" target="_blank" class="footer__instagram"><?php the_field('instagram', 'option'); ?></a>
            <?php } ?>
            <?php $email = get_field('email', 'option'); ?>
            <?php if($email){ ?>
            <a href="mailto:<?php echo $email; ?>" class="footer__mail"><?php echo $email; ?></a>
            <?php } ?>
         </div>
      </div>
      <div class="footer__union footer__union_top">
         <?php the_field('design_and_development', 'option'); ?>
         <div class="footer__description">
            <?php the_field('copyright', 'option'); ?>
         </div>
      </div>
   </div>
    <div class="quiz-option">
        <div class="quiz-option__bg"></div>
        <div class="quiz-option__wrap">
            <div class="quiz-option__content">
                <div class="quiz-option__close">
                    <svg width="27" height="27" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.744141 25.0061L2.01901 26.2811L13.7438 14.5563L25.4689 26.2812L26.7442 25.0062L15.0191 13.2811L26.7438 1.55644L25.4685 0.28125L13.7442 12.006L2.01944 0.281463L0.744141 1.55666L12.4689 13.2812L0.744141 25.0061Z" fill="#383838"/>
                    </svg>
                </div>
                <div class="quiz-option__title">
                    Please select the type of book you wish to order
                </div>
                <div class="quiz-option__list">
                    <label class="quiz-option__item selected">
                <span class="quiz-option__item-icon">
                  <svg width="39" height="39" viewBox="0 0 39 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M18.3066 7.29505C14.0389 4.7344 8.81942 4.31618 4.19839 6.16459C3.52533 6.43382 3.08398 7.0857 3.08398 7.81061V25.5765C3.08398 26.7666 4.28591 27.5803 5.39083 27.1383C9.17497 25.6247 13.4492 25.9672 16.944 28.0641L18.6739 29.102C18.8059 29.1812 18.947 29.2158 19.084 29.2134C19.221 29.2158 19.3621 29.1812 19.494 29.102L21.224 28.0641C24.7188 25.9672 28.993 25.6247 32.7771 27.1383C33.8821 27.5803 35.084 26.7666 35.084 25.5765V7.81061C35.084 7.0857 34.6426 6.43382 33.9696 6.16459C29.3486 4.31618 24.1291 4.7344 19.8614 7.29505L19.084 7.76148L18.3066 7.29505ZM20.2268 10.9357C20.2268 10.3045 19.7152 9.79286 19.084 9.79286C18.4528 9.79286 17.9411 10.3045 17.9411 10.9357V25.4119C17.9411 26.0431 18.4528 26.5548 19.084 26.5548C19.7152 26.5548 20.2268 26.0431 20.2268 25.4119V10.9357Z" fill="#408D5C"/>
                      <path d="M4.95041 30.0477C8.03438 28.2487 11.8479 28.2487 14.9318 30.0477L16.5886 31.0141C18.1306 31.9136 20.0374 31.9136 21.5793 31.0141L23.2361 30.0477C26.3201 28.2487 30.1336 28.2487 33.2176 30.0477L33.3741 30.139C33.9193 30.4571 34.1035 31.1568 33.7854 31.702C33.4674 32.2473 32.7676 32.4314 32.2224 32.1134L32.0659 32.022C29.6936 30.6382 26.7601 30.6382 24.3878 32.022L22.7311 32.9885C20.4774 34.3031 17.6906 34.3031 15.4369 32.9885L13.7801 32.022C11.4079 30.6382 8.4744 30.6382 6.10211 32.022L5.94555 32.1134C5.40035 32.4314 4.70056 32.2473 4.38252 31.702C4.06449 31.1568 4.24864 30.4571 4.79385 30.139L4.95041 30.0477Z" fill="#408D5C"/>
                  </svg>
                </span>
                        <input checked value="printed"  type="radio" name="option" class="quiz-option__item-input">
                        <span class="quiz-option__item-title">Printed Book</span>
                    </label>
                    <label class="quiz-option__item">
                <span class="quiz-option__item-icon">
                  <svg width="39" height="39" viewBox="0 0 39 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M27.3098 13.6932L17.6328 4.01624V11.7576C17.6328 12.8282 18.4979 13.6932 19.5684 13.6932H27.3098Z" fill="#346E49"/>
                    <path d="M14.7295 25.3058C14.1976 25.3058 13.7617 25.7417 13.7617 26.2736V31.1125V34.0159C13.7617 34.5478 14.1976 34.9837 14.7295 34.9837C15.2614 34.9837 15.6973 34.5478 15.6973 34.0159V32.0803H16.6651C18.5344 32.0803 20.0524 30.5624 20.0524 28.6931C20.0524 26.8237 18.5344 25.3058 16.6651 25.3058H14.7295ZM16.6651 30.1447H15.6973V27.2414H16.6651C17.4697 27.2414 18.1168 27.8885 18.1168 28.6931C18.1168 29.4976 17.4697 30.1447 16.6651 30.1447ZM22.4719 25.3058C21.94 25.3058 21.5041 25.7417 21.5041 26.2736V34.015C21.5041 34.5469 21.94 34.9828 22.4719 34.9828H24.4075C26.0099 34.9828 27.3109 33.6828 27.3109 32.0794V28.2082C27.3109 26.6058 26.0109 25.3048 24.4075 25.3048H22.4719V25.3058ZM24.4065 33.0481H23.4387V27.2414H24.4065C24.9384 27.2414 25.3743 27.6773 25.3743 28.2092V32.0803C25.3743 32.6122 24.9394 33.0481 24.4065 33.0481ZM29.2455 26.2736V30.1447V34.0159C29.2455 34.5478 29.6814 34.9837 30.2133 34.9837C30.7452 34.9837 31.1811 34.5478 31.1811 34.0159V31.1125H33.1167C33.6486 31.1125 34.0845 30.6767 34.0845 30.1447C34.0845 29.6128 33.6486 29.177 33.1167 29.177H31.1811V27.2414H33.1167C33.6486 27.2414 34.0845 26.8055 34.0845 26.2736C34.0845 25.7417 33.6486 25.3058 33.1167 25.3058H30.2133C29.6814 25.3058 29.2455 25.7417 29.2455 26.2736Z" fill="#408D5C"/>
                    <path d="M4.08398 7.88742C4.08398 5.75212 5.81987 4.01624 7.95517 4.01624H17.6322V11.7576C17.6322 12.8282 18.4972 13.6932 19.5678 13.6932H27.3092V22.4024H14.7288C12.5935 22.4024 10.8576 24.1383 10.8576 26.2736V34.9828H7.95517C5.81987 34.9838 4.08398 33.2479 4.08398 31.1126V7.88742Z" fill="#408D5C"/>
                  </svg>
                </span>
                        <input type="radio" value="digital" name="option" class="quiz-option__item-input">
                        <span class="quiz-option__item-title">PDF (digital) Book</span>
                    </label>
                </div>
                <a href="#" class="quiz-option__action">
                    <span class="quiz-option__action-title">Confirm</span>
                    <span class="quiz-option__action-icon">
            <svg width="15" height="11" viewBox="0 0 15 11" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M14.4141 0.986021C14.7422 1.31406 14.7422 1.84592 14.4141 2.17396L6.57413 10.014C6.24609 10.342 5.71423 10.342 5.38619 10.014L0.906187 5.53396C0.578146 5.20592 0.578146 4.67406 0.906187 4.34602C1.23423 4.01798 1.76609 4.01798 2.09413 4.34602L5.98016 8.23205L13.2262 0.986021C13.5542 0.65798 14.0861 0.65798 14.4141 0.986021Z" fill="#FBFAF7"/>
            </svg>
          </span>
                </a>
            </div>
        </div>
    </div>
</footer>
</main>
<?php wp_footer(); ?>
</body>
</html>
