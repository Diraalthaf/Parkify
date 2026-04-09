(function ($) {
    "use strict";

    /*=============================================
    [ VALIDATE FORM ]
    =============================================*/
    var input = $('.validate-input .input100');

    $('.validate-form').on('submit', function () {
        var check = true;

        for (var i = 0; i < input.length; i++) {
            if (validate(input[i]) == false) {
                showValidate(input[i]);
                check = false;
            }
        }

        return check;
    });

    $('.validate-form .input100').each(function () {
        $(this).focus(function () {
            hideValidate(this);
        });
    });

    function validate(input) {
        if ($(input).val().trim() == '') {
            return false;
        }
    }

    function showValidate(input) {
        var thisAlert = $(input).parent();
        $(thisAlert).addClass('alert-validate');
    }

    function hideValidate(input) {
        var thisAlert = $(input).parent();
        $(thisAlert).removeClass('alert-validate');
    }

    /*=============================================
    [ SHOW / HIDE PASSWORD ]
    =============================================*/
    const passwordInput = document.querySelector('input[name="password"]');

    if (passwordInput) {
        // buat icon mata otomatis
        const eyeIcon = document.createElement('i');
        eyeIcon.classList.add('fa', 'fa-eye-slash');
        eyeIcon.style.position = 'absolute';
        eyeIcon.style.right = '25px';
        eyeIcon.style.top = '50%';
        eyeIcon.style.transform = 'translateY(-50%)';
        eyeIcon.style.cursor = 'pointer';
        eyeIcon.style.color = '#999';

        // masukin ke parent input
        const parent = passwordInput.parentElement;
        parent.style.position = 'relative';
        parent.appendChild(eyeIcon);

        eyeIcon.addEventListener('click', () => {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        });
    }

})(jQuery);