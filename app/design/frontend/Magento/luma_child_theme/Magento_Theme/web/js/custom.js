require(["jquery", "owlcarousel"], function ($) {
    $(document).ready(function () {
        $('.homeSlider').owlCarousel({
            loop: true,
            margin: 10,
            stagePadding: 50,
            responsiveClass: true,
            pagination: false,
            autoplay: false,
            stopOnHover: true,
            navigation: true,
            navigationText: ["prev", "next"],
            rewindNav: true,
            scrollPerPage: false,
            animateOut: 'fadeOut',
            autoplayTimeout: 5000,
            smartSpeed: 1000,
            responsive: {
                0: {
                    items: 1,
                    nav: false,
                    loop: true
                },
                600: {
                    items:1,
                    nav: false
                },
                1000: {
                    items: 1,
                    nav: true,
                    loop: true
                }
            }
        });
    });
});

require(["jquery", "owlcarousel"], function ($) {
    $(document).ready(function () {
        $('.featured-slider').owlCarousel({
            loop: true,
            margin: 10,
            stagePadding: 50,
            responsiveClass: true,
            pagination: false,
            autoplay: false,
            stopOnHover: true,
            navigation: true,
            navigationText: ["prev", "next"],
            rewindNav: true,
            scrollPerPage: false,
            animateOut: 'fadeOut',
            autoplayTimeout: 5000,
            smartSpeed: 1000,
            responsive: {
                0: {
                    items: 1,
                    nav: false,
                    loop: true
                },
                600: {
                    items: 2,
                    nav: false
                },
                1000: {
                    items: 2,
                    nav: true,
                    loop: true
                }
            }
        });
        $( ".owl-prev").html('<span class="custom-prev"></span>');
        $( ".owl-next").html('<span class="custom-next"></span>');
    });
});
require(["jquery", "owlcarousel"], function ($) {
    $(document).ready(function () {
        $('.relateslider').owlCarousel({
            loop: true,
            margin: 10,
            responsiveClass: true,
            pagination: false,
            autoplay: false,
            stopOnHover: true,
            navigation: true,
            navigationText: ["prev", "next"],
            rewindNav: true,
            scrollPerPage: false,
            animateOut: 'fadeOut',
            autoplayTimeout: 5000,
            smartSpeed: 1000,
            responsive: {
                0: {
                    items: 1,
                    nav: false,
                    loop: true
                },
                600: {
                    items: 2,
                    nav: false
                },
                1000: {
                    items: 4,
                    nav: true,
                    loop: true
                }
            }
        });
        $( ".owl-prev").html('<span class="custom-prev"></span>');
        $( ".owl-next").html('<span class="custom-next"></span>');
    });
});
require(["jquery", "owlcarousel"], function ($) {
    $(document).ready(function () {
        $('.videoSlider').owlCarousel({
            loop: true,
            margin: 10,
            responsiveClass: true,
            pagination: false,
            autoplay: false,
            stopOnHover: true,
            navigation: true,
            navigationText: ["prev", "next"],
            rewindNav: true,
            scrollPerPage: false,
            animateOut: 'fadeOut',
            autoplayTimeout: 5000,
            smartSpeed: 1000,
            responsive: {
                0: {
                    items: 1,
                    nav: false
                },
                600: {
                    items: 1,
                    nav: true
                },
                1000: {
                    items: 1,
                    nav: true,
                    loop: true
                }
            }
        });
        $( ".owl-prev").html('<span class="custom-prev"></span>');
        $( ".owl-next").html('<span class="custom-next"></span>');
    });
});
require(["jquery", "owlcarousel"], function ($) {
    $(document).ready(function () {
        $('.depositSlider').owlCarousel({
            loop: true,
            margin: 10,
            stagePadding: 50,
            responsiveClass: true,
            pagination: false,
            autoplay: false,
            stopOnHover: true,
            navigation: true,
            navigationText: ["prev", "next"],
            rewindNav: true,
            scrollPerPage: false,
            animateOut: 'fadeOut',
            autoplayTimeout: 5000,
            smartSpeed: 1000,
            responsive: {
                0: {
                    items: 1,
                    nav: false,
                    loop: true
                },
                600: {
                    items: 2,
                    nav: false
                },
                1000: {
                    items: 3,
                    nav: true,
                    loop: true
                }
            }
        });
        $( ".owl-prev").html('<span class="custom-prev"></span>');
        $( ".owl-next").html('<span class="custom-next"></span>');
    });
});
require(["jquery", "owlcarousel"], function ($) {
    $(document).ready(function () {
        $('.deposlider').owlCarousel({
            loop: true,
            margin: 10,
            responsiveClass: true,
            pagination: false,
            autoplay: false,
            stopOnHover: true,
            navigation: true,
            navigationText: ["prev", "next"],
            rewindNav: true,
            scrollPerPage: false,
            animateOut: 'fadeOut',
            autoplayTimeout: 5000,
            smartSpeed: 1000,
            responsive: {
                0: {
                    items: 1,
                    nav: false,
                    stagePadding: 60,
                    loop: true
                },
                600: {
                    items: 2,
                    stagePadding: 60,
                    nav: false
                },
                1000: {
                    items: 4,
                    nav: true,
                    loop: true
                }
            }
        });
        $( ".owl-prev").html('<span class="custom-prev"></span>');
        $( ".owl-next").html('<span class="custom-next"></span>');
    });
});
require(["jquery", "owlcarousel"], function ($) {
    $(document).ready(function () {
        $('.blogs-slider').owlCarousel({
            loop: true,
            margin: 20,
            responsiveClass: true,
            pagination: true,
            autoplay: false,
            stopOnHover: true,
            navigation: true,
            navigationText: ["prev", "next"],
            rewindNav: true,
            scrollPerPage: false,
            animateOut: 'fadeOut',
            autoplayTimeout: 5000,
            smartSpeed: 1000,
            responsive: {
                0: {
                    items: 1,
                    nav: true
                },
                600: {
                    items: 2,
                    nav: true
                },
                1000: {
                    items: 4,
                    nav: true,
                    loop: true
                }
            }
        });
        $(".blogs-slider .owl-prev").html('<img src="http://printbag.speedboost.xyz/pub/media/about/about-left.png">');
        $(".blogs-slider .owl-next").html('<img src="http://printbag.speedboost.xyz/pub/media/about/about-right.png">');
    });
});
require(["jquery", "owlcarousel"], function ($) {
    $(document).ready(function () {
        $("#bundle-slide").click(function(){
            $("#print-boption").toggleClass("someClass");
        });
        $("#kind_of_person5460").click(function(){
            if ($(this).is(':checked'))
            {
                $("#cnpj").toggle();
                $("#cpf").hide();
            }
            else {
                $("#cnpj").toggle();
            }
        });
        $("#kind_of_person5459").click(function(){
            if ($(this).is(':checked'))
            {
                $("#cpf").toggle();
                $("#cnpj").hide();
            }
            else {
                $("#cpf").toggle();
            }
        });
        $(document).ready(function(e) {
            $(".header.content .block.block-search input#search").click(function(event) {
                $(".header.content").addClass("main");
                event.stopPropagation();
            });
            $(document).click(function(event){
                if (!$(event.target).hasClass('main')) {
                    $(".header.content.main").removeClass("main");
                }
            });
        });
    });
});