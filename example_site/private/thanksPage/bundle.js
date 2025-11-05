/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
var __webpack_exports__ = {};

$(document).ready(function () {
  var modal = document.querySelector('.modal'),
    overlay = document.querySelector('.overlay');
  function openModal() {
    var btns = document.querySelectorAll('.btn');
    btns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var title = btn.getAttribute('data-title');
        if (title) {
          modal.querySelector('.modal-subtitle').textContent = title;
        }
        modal.classList.add('active');
        overlay.classList.add('active');
      });
    });
  }
  function closeModal() {
    overlay.addEventListener('click', function () {
      modal.classList.remove('active');
      overlay.classList.remove('active');
    });
  }
  openModal();
  closeModal();
});
/******/ })()
;