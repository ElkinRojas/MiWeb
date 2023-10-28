/**
* Template Name: PhotoFolio
* Updated: Sep 18 2023 with Bootstrap v5.3.2
* Template URL: https://bootstrapmade.com/photofolio-bootstrap-photography-website-template/
* Author: BootstrapMade.com
* License: https://bootstrapmade.com/license/
*/
document.addEventListener('DOMContentLoaded', () => {
  "use strict";

  /**
   * Preloader
   */
  const preloader = document.querySelector('#preloader');
  if (preloader) {
    window.addEventListener('load', () => {
      setTimeout(() => {
        preloader.classList.add('loaded');
      }, 1000);
      setTimeout(() => {
        preloader.remove();
      }, 2000);
    });
  }

  /**
   * Mobile nav toggle
   */
  const mobileNavShow = document.querySelector('.mobile-nav-show');
  const mobileNavHide = document.querySelector('.mobile-nav-hide');

  document.querySelectorAll('.mobile-nav-toggle').forEach(el => {
    el.addEventListener('click', function(event) {
      event.preventDefault();
      mobileNavToogle();
    })
  });

  function mobileNavToogle() {
    document.querySelector('body').classList.toggle('mobile-nav-active');
    mobileNavShow.classList.toggle('d-none');
    mobileNavHide.classList.toggle('d-none');
  }

  /**
   * Hide mobile nav on same-page/hash links
   */
  document.querySelectorAll('#navbar a').forEach(navbarlink => {

    if (!navbarlink.hash) return;

    let section = document.querySelector(navbarlink.hash);
    if (!section) return;

    navbarlink.addEventListener('click', () => {
      if (document.querySelector('.mobile-nav-active')) {
        mobileNavToogle();
      }
    });

  });

  /**
   * Toggle mobile nav dropdowns
   */
  const navDropdowns = document.querySelectorAll('.navbar .dropdown > a');

  navDropdowns.forEach(el => {
    el.addEventListener('click', function(event) {
      if (document.querySelector('.mobile-nav-active')) {
        event.preventDefault();
        this.classList.toggle('active');
        this.nextElementSibling.classList.toggle('dropdown-active');

        let dropDownIndicator = this.querySelector('.dropdown-indicator');
        dropDownIndicator.classList.toggle('bi-chevron-up');
        dropDownIndicator.classList.toggle('bi-chevron-down');
      }
    })
  });

  /**
   * Scroll top button
   */
  const scrollTop = document.querySelector('.scroll-top');
  if (scrollTop) {
    const togglescrollTop = function() {
      window.scrollY > 100 ? scrollTop.classList.add('active') : scrollTop.classList.remove('active');
    }
    window.addEventListener('load', togglescrollTop);
    document.addEventListener('scroll', togglescrollTop);
    scrollTop.addEventListener('click', window.scrollTo({
      top: 0,
      behavior: 'smooth'
    }));
  }

  /**
   * Initiate glightbox
   */
  const glightbox = GLightbox({
    selector: '.glightbox'
  });

  /**
   * Init swiper slider with 1 slide at once in desktop view
   */
  new Swiper('.slides-1', {
    speed: 600,
    loop: true,
    autoplay: {
      delay: 5000,
      disableOnInteraction: false
    },
    slidesPerView: 'auto',
    pagination: {
      el: '.swiper-pagination',
      type: 'bullets',
      clickable: true
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    }
  });

  /**
   * Init swiper slider with 3 slides at once in desktop view
   */
  new Swiper('.slides-3', {
    speed: 600,
    loop: true,
    autoplay: {
      delay: 5000,
      disableOnInteraction: false
    },
    slidesPerView: 'auto',
    pagination: {
      el: '.swiper-pagination',
      type: 'bullets',
      clickable: true
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    breakpoints: {
      320: {
        slidesPerView: 1,
        spaceBetween: 40
      },

      1200: {
        slidesPerView: 3,
      }
    }
  });

  /**
   * Animation on scroll function and init
   */
  function aos_init() {
    AOS.init({
      duration: 1000,
      easing: 'ease-in-out',
      once: true,
      mirror: false
    });
  }
  window.addEventListener('load', () => {
    aos_init();
  });

});

var show_alert = function(title, text, theme, wait) {
  var _wait = wait || 1;

  swal.fire({
      title: title,
      icon: theme,
      //text: text,
      html: text,
      showConfirmButton: false,
      timer: _wait*1000
  });
};


$('body').on('submit', '#form_user_vote', function(e) {
  e.preventDefault();
  var vote = $('#vote').val();
  var code = $('#code').val();
  var phone = $('#email').val();
  var name_restaurante = $("#vote option:selected").text();
  var parameters = {
    action: "send",
    table: "vote",
    code: code,
    phone: phone,
    vote : vote,
    name_restaurante: name_restaurante
  };

  $.ajax({
      type: "POST",
      url: "application/controllers/pina_control.php",
      data: $.param(parameters),
      dataType: "json",
      success: function(result) {
          if(result.is_success === 1 ){
            show_alert("¡Bien!", "Su voto ha sido validado correctamente", "success");

            setTimeout(function () {
              location.reload();
            }, 1000);
          }else{
            show_alert("¡Error!", result.message, "error", 5);
          }
            
      },error :function(){
          show_alert("¡Error!", "Se presento un error desconocido", "error", 5);
      }
  });
});

$('body').on('click', '#boton-busqueda', function(e) {
  e.preventDefault();
  const busqueda = $('#input-busqueda').val().toLowerCase();
  buscarImagenes(busqueda);
});

function buscarImagenes(palabraClave) {
  // Itera a través de las imágenes y muestra u oculta según la palabra clave
  const imagenes = document.querySelectorAll('.contenedor-imagenes img');
  imagenes.forEach((imagen) => {
  const alt = imagen.alt.toLowerCase();
  if (alt.includes(palabraClave) || palabraClave === '') {
      imagen.style.display = 'block';
  } else {
      imagen.style.display = 'none';
  }
  });
}

$('body').on('click', '.send_code', function(e) {
  e.preventDefault();
  const phone = $('#email').val();
  if (phone != "") {
    var parameters = {
      action: "send",
      table: "code",
      phone : phone
    };

    $.ajax({
        type: "POST",
        url: "application/controllers/pina_control.php",
        data: $.param(parameters),
        //dataType: "json",
        success: function(result) {
            if( result ){
              show_alert("¡Bien!", "Código enviado con exito", "success");
            }else{
              show_alert("¡Error!", "Solo se permite votar una vez por número de celular", "error", 5);
            }
        },error :function(){
          show_alert("¡Error!", "Se presento un error desconocido", "error", 5);
        }
    });
  } else {
    show_alert("¡Alerta!", "Por favor introducir un número de celular", "warning", 5);
  }
});

$('body').on('submit', '#forms_contact', function(e) {
  e.preventDefault();
  var name = $('#name').val();
  var email = $('#email').val();
  var subject = $('#subject').val();
  var message = $('#message').val();
  var parameters = {
    action: "send",
    table: "email",
    name: name,
    email: email,
    subject : subject,
    message: message
  };

  $.ajax({
      type: "POST",
      url: "application/controllers/pina_control.php",
      data: $.param(parameters),
      dataType: "json",
      success: function(result) {
          if(result.is_success === 1 ){
            show_alert("¡Bien!", "Su correo ha sido enviado correctamente", "success");

            setTimeout(function () {
              location.reload();
            }, 1000);
          }else{
            show_alert("¡Error!", result.message, "error", 5);
          }
            
      },error :function(){
          show_alert("¡Error!", "Se presento un error desconocido", "error", 5);
      }
  });
});