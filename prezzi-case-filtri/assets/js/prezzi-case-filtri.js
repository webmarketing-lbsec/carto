(function () {
  'use strict';

  function getData() {
    if (typeof window.pcfData !== 'object' || window.pcfData === null) {
      return null;
    }
    return window.pcfData;
  }

  function serializeFilters(form) {
    var filters = {};
    var selects = form.querySelectorAll('select[data-filter-key]');

    selects.forEach(function (select) {
      filters[select.getAttribute('data-filter-key')] = select.value;
    });

    return filters;
  }

  function setLoading(container, message) {
    container.innerHTML = '<p class="pcf-loading">' + message + '</p>';
  }

  function runSearch(form, resultsContainer) {
    var data = getData();
    if (!data) {
      return;
    }

    setLoading(resultsContainer, data.labels.loading);

    var body = new URLSearchParams();
    body.append('action', data.action);
    body.append('nonce', data.nonce);

    var filters = serializeFilters(form);
    Object.keys(filters).forEach(function (key) {
      body.append('filters[' + key + ']', filters[key]);
    });

    fetch(data.ajaxUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
      },
      body: body.toString()
    })
      .then(function (response) {
        return response.json();
      })
      .then(function (json) {
        if (!json || json.success !== true || !json.data || !json.data.html) {
          resultsContainer.innerHTML = '<p class="pcf-error">' + data.labels.error + '</p>';
          return;
        }

        resultsContainer.innerHTML = json.data.html;
      })
      .catch(function () {
        resultsContainer.innerHTML = '<p class="pcf-error">' + data.labels.error + '</p>';
      });
  }

  document.addEventListener('DOMContentLoaded', function () {
    var wrappers = document.querySelectorAll('.pcf-wrapper');

    wrappers.forEach(function (wrapper) {
      var form = wrapper.querySelector('.pcf-form');
      var resultsContainer = wrapper.querySelector('.pcf-results');

      if (!form || !resultsContainer) {
        return;
      }

      form.addEventListener('submit', function (event) {
        event.preventDefault();
        runSearch(form, resultsContainer);
      });

      form.addEventListener('reset', function () {
        window.setTimeout(function () {
          resultsContainer.innerHTML = '';
        }, 0);
      });
    });
  });
})();
