// code to hide placeholder on focus event and displays it on blur event
$(function () {
  'use strict';
  //dashboard Menu
  $('.toggle-menu').click(function () {
    $(this).toggleClass('selected').parent().next('.body-menu').fadeToggle(200);
    if ($(this).hasClass('selected')) {
      $(this).html('<i class="fa fa-plus float-right"></i>');
    } else {
      $(this).html('<i class="fa fa-minus float-right"></i>');
    }
  });
  // hide placeholders on form focus
  $('[placeholder]')
    .focus(function () {
      $(this).attr('data-text', $(this).attr('placeholder'));
      $(this).attr('placeholder', ' ');
    })
    .blur(function () {
      $(this).attr('placeholder', $(this).attr('data-text'));
    });

  // add Asterisk on required field
  $('input').each(function () {
    if ($(this).attr('required') === 'required') {
      $(this).after('<span class="asterisk">*</span>');
    }
  });
  //convert password field to text
  let passfield = $('.password');
  $('.show-pass').hover(
    function () {
      passfield.attr('type', 'text');
    },
    function () {
      passfield.attr('type', 'password');
    }
  );
  //confirm message on delete
  $('.confirm').click(function () {
    return confirm('are you sure ?');
  });
  // Category View Option
  $('.cat h3').click(function () {
    $(this).next('.full-view').fadeToggle(300);
  });
  // Model View
  $('.view span').click(function () {
    $(this).addClass('active').siblings('span').removeClass('active');
    // check which mode should displayed
    if ($(this).data('view') === 'full') {
      $('.cat .full-view').fadeIn(200);
    } else {
      $('.cat .full-view').fadeOut(200);
    }
  });
});
