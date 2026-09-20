/**
 * Paginador Universal de Tablas del Sistema de Nómina
 * Soporta selección dinámica de registros por página, sincronización con filtros en vivo,
 * persistencia en localStorage y responsividad completa.
 */
(function(window) {
  'use strict';

  function TablePaginator(options) {
    this.table = typeof options.table === 'string' ? document.querySelector(options.table) : options.table;
    if (!this.table) {
      console.warn('TablePaginator: Tabla no encontrada', options.table);
      return;
    }

    this.storageKey = options.storageKey || ('paginator_' + (this.table.id || 'default'));
    this.pageSizeOptions = options.pageSizeOptions || [5, 10, 25, 50, 100];
    this.allowAll = options.allowAll !== false; // Incluir opción "Todos"
    
    // Cargar preferencia guardada o valor por defecto
    var savedSize = localStorage.getItem(this.storageKey);
    this.pageSize = savedSize ? parseInt(savedSize, 10) : (options.defaultPageSize || 10);
    this.currentPage = 1;

    this.appendTo = options.appendTo || null;
    this.insertAfter = options.insertAfter || null;
    this.rowSelector = options.rowSelector || 'tbody tr';
    this.noResultsSelector = options.noResultsSelector || null;

    this.onPageChange = options.onPageChange || null;
    this.container = null;

    this.init();
  }

  TablePaginator.prototype.init = function() {
    this.buildUI();
    this.update();
  };

  TablePaginator.prototype.buildUI = function() {
    // Si ya existía un contenedor previo, retirarlo
    if (this.container && this.container.parentElement) {
      this.container.parentElement.removeChild(this.container);
    }

    var wrap = document.createElement('div');
    wrap.className = 'custom-paginator-wrap';
    wrap.setAttribute('role', 'navigation');
    wrap.setAttribute('aria-label', 'Controles de paginación');

    // 1. Selector de tamaño
    var sizeBox = document.createElement('div');
    sizeBox.className = 'paginator-size-box';

    var sizeLabel = document.createElement('label');
    sizeLabel.textContent = 'Mostrar';
    var uniqueId = 'paginator_size_' + Math.random().toString(36).substring(2, 9);
    sizeLabel.setAttribute('for', uniqueId);

    var select = document.createElement('select');
    select.id = uniqueId;
    select.className = 'paginator-select';
    select.setAttribute('aria-label', 'Cantidad de registros por página');

    var self = this;
    this.pageSizeOptions.forEach(function(opt) {
      var option = document.createElement('option');
      option.value = opt;
      option.textContent = opt;
      if (opt === self.pageSize) option.selected = true;
      select.appendChild(option);
    });

    if (this.allowAll) {
      var optAll = document.createElement('option');
      optAll.value = '0';
      optAll.textContent = 'Todos';
      if (self.pageSize === 0) optAll.selected = true;
      select.appendChild(optAll);
    }

    select.addEventListener('change', function(e) {
      var newSize = parseInt(e.target.value, 10);
      self.setPageSize(newSize);
    });

    var sizeSuffix = document.createElement('span');
    sizeSuffix.textContent = 'registros por página';

    sizeBox.appendChild(sizeLabel);
    sizeBox.appendChild(select);
    sizeBox.appendChild(sizeSuffix);

    // 2. Info de registros
    var infoBox = document.createElement('div');
    infoBox.className = 'paginator-info';
    infoBox.textContent = 'Cargando registros…';

    // 3. Botones de navegación
    var navBox = document.createElement('div');
    navBox.className = 'paginator-nav';

    wrap.appendChild(sizeBox);
    wrap.appendChild(infoBox);
    wrap.appendChild(navBox);

    this.container = wrap;
    this.sizeSelect = select;
    this.infoBox = infoBox;
    this.navBox = navBox;

    // Inserción en el DOM
    if (this.appendTo) {
      var target = typeof this.appendTo === 'string' ? document.querySelector(this.appendTo) : this.appendTo;
      if (target) {
        target.appendChild(wrap);
        return;
      }
    }
    if (this.insertAfter) {
      var afterEl = typeof this.insertAfter === 'string' ? document.querySelector(this.insertAfter) : this.insertAfter;
      if (afterEl && afterEl.parentNode) {
        afterEl.parentNode.insertBefore(wrap, afterEl.nextSibling);
        return;
      }
    }
    if (this.table && this.table.parentNode) {
      this.table.parentNode.appendChild(wrap);
    }
  };

  /**
   * Obtiene las filas de datos que no son filas auxiliares (como no data o no results)
   */
  TablePaginator.prototype.getDataRows = function() {
    var allRows = Array.from(this.table.querySelectorAll(this.rowSelector));
    return allRows.filter(function(row) {
      // Ignorar filas de estado vacío
      if (row.id && (row.id.includes('NoData') || row.id.includes('NoResults') || row.id.includes('noData') || row.id.includes('noResults'))) {
        return false;
      }
      if (row.classList.contains('no-data-row') || row.classList.contains('empty-row')) {
        return false;
      }
      return true;
    });
  };

  /**
   * Obtiene las filas que pasan los filtros de búsqueda externos
   */
  TablePaginator.prototype.getFilteredRows = function() {
    var rows = this.getDataRows();
    return rows.filter(function(row) {
      // Si el filtro externo lo marcó explícitamente como oculto por búsqueda
      if (row.dataset.filteredOut === 'true') {
        return false;
      }
      return true;
    });
  };

  TablePaginator.prototype.setPageSize = function(size) {
    this.pageSize = size;
    localStorage.setItem(this.storageKey, size);
    this.currentPage = 1;
    this.update();
    if (this.onPageChange) this.onPageChange(this.currentPage, this.pageSize);
  };

  TablePaginator.prototype.goToPage = function(page) {
    var totalPages = this.getTotalPages();
    if (page < 1) page = 1;
    if (totalPages > 0 && page > totalPages) page = totalPages;
    this.currentPage = page;
    this.update();
    if (this.onPageChange) this.onPageChange(this.currentPage, this.pageSize);
  };

  TablePaginator.prototype.getTotalPages = function() {
    var visibleRows = this.getFilteredRows();
    if (this.pageSize <= 0 || visibleRows.length === 0) return 1;
    return Math.ceil(visibleRows.length / this.pageSize);
  };

  /**
   * Recalcula la visualización de filas, paginación e información
   */
  TablePaginator.prototype.update = function() {
    if (!this.container) return;

    var self = this;
    var allDataRows = this.getDataRows();
    var filteredRows = this.getFilteredRows();
    var totalFiltered = filteredRows.length;
    var totalAll = allDataRows.length;

    var totalPages = this.pageSize <= 0 ? 1 : Math.ceil(totalFiltered / this.pageSize);
    if (this.currentPage > totalPages && totalPages > 0) {
      this.currentPage = totalPages;
    }
    if (this.currentPage < 1) {
      this.currentPage = 1;
    }

    // 1. Mostrar/ocultar filas de la tabla
    var startIndex = (this.currentPage - 1) * this.pageSize;
    var endIndex = this.pageSize <= 0 ? totalFiltered : Math.min(startIndex + this.pageSize, totalFiltered);

    // Ocultar todas las filas que no coincidan con el filtro
    allDataRows.forEach(function(row) {
      if (row.dataset.filteredOut === 'true') {
        row.style.display = 'none';
      }
    });

    // En las filas que coinciden, mostrar solo la ventana actual
    filteredRows.forEach(function(row, idx) {
      if (self.pageSize <= 0 || (idx >= startIndex && idx < endIndex)) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });

    // Manejo de fila "No hay resultados"
    if (this.noResultsSelector) {
      var noResultsRow = document.querySelector(this.noResultsSelector);
      if (noResultsRow) {
        noResultsRow.style.display = (totalFiltered === 0 && totalAll > 0) ? '' : 'none';
      }
    }

    // 2. Actualizar texto informativo
    if (totalFiltered === 0) {
      this.infoBox.innerHTML = 'Sin registros que mostrar';
      this.container.style.display = totalAll === 0 ? 'none' : 'flex';
    } else {
      this.container.style.display = 'flex';
      var from = startIndex + 1;
      var to = this.pageSize <= 0 ? totalFiltered : endIndex;

      if (totalFiltered < totalAll) {
        this.infoBox.innerHTML = 'Mostrando <strong>' + from + '</strong> &ndash; <strong>' + to + '</strong> de <strong>' + totalFiltered + '</strong> filtrados (de ' + totalAll + ' totales)';
      } else {
        this.infoBox.innerHTML = 'Mostrando <strong>' + from + '</strong> &ndash; <strong>' + to + '</strong> de <strong>' + totalAll + '</strong> registros';
      }
    }

    // 3. Renderizar botones de navegación
    this.renderNavButtons(totalPages);
  };

  TablePaginator.prototype.renderNavButtons = function(totalPages) {
    this.navBox.innerHTML = '';
    var self = this;

    if (totalPages <= 1) {
      // Mantener simetría visual en 1 sola página
      var btnPrevDis = document.createElement('button');
      btnPrevDis.type = 'button';
      btnPrevDis.className = 'paginator-btn disabled';
      btnPrevDis.disabled = true;
      btnPrevDis.setAttribute('aria-label', 'Página anterior');
      btnPrevDis.innerHTML = '<i class="fa-solid fa-chevron-left"></i>';
      this.navBox.appendChild(btnPrevDis);

      var pOnly = document.createElement('button');
      pOnly.type = 'button';
      pOnly.className = 'paginator-btn active';
      pOnly.textContent = '1';
      pOnly.setAttribute('aria-current', 'page');
      this.navBox.appendChild(pOnly);

      var btnNextDis = document.createElement('button');
      btnNextDis.type = 'button';
      btnNextDis.className = 'paginator-btn disabled';
      btnNextDis.disabled = true;
      btnNextDis.setAttribute('aria-label', 'Página siguiente');
      btnNextDis.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
      this.navBox.appendChild(btnNextDis);
      return;
    }

    // Botón Primera Página («)
    var btnFirst = document.createElement('button');
    btnFirst.type = 'button';
    btnFirst.className = 'paginator-btn' + (this.currentPage === 1 ? ' disabled' : '');
    btnFirst.disabled = this.currentPage === 1;
    btnFirst.title = 'Primera página';
    btnFirst.setAttribute('aria-label', 'Ir a primera página');
    btnFirst.innerHTML = '<i class="fa-solid fa-angles-left"></i>';
    btnFirst.onclick = function() { self.goToPage(1); };
    this.navBox.appendChild(btnFirst);

    // Botón Anterior (‹)
    var btnPrev = document.createElement('button');
    btnPrev.type = 'button';
    btnPrev.className = 'paginator-btn' + (this.currentPage === 1 ? ' disabled' : '');
    btnPrev.disabled = this.currentPage === 1;
    btnPrev.title = 'Página anterior';
    btnPrev.setAttribute('aria-label', 'Página anterior');
    btnPrev.innerHTML = '<i class="fa-solid fa-chevron-left"></i>';
    btnPrev.onclick = function() { self.goToPage(self.currentPage - 1); };
    this.navBox.appendChild(btnPrev);

    // Rango dinámico de números de página
    var maxVisibleButtons = 5;
    var startPage = Math.max(1, this.currentPage - 2);
    var endPage = Math.min(totalPages, startPage + maxVisibleButtons - 1);

    if (endPage - startPage < maxVisibleButtons - 1) {
      startPage = Math.max(1, endPage - maxVisibleButtons + 1);
    }

    if (startPage > 1) {
      var p1 = this.createPageBtn(1);
      this.navBox.appendChild(p1);
      if (startPage > 2) {
        var elipsis = document.createElement('span');
        elipsis.className = 'paginator-ellipsis';
        elipsis.textContent = '…';
        this.navBox.appendChild(elipsis);
      }
    }

    for (var p = startPage; p <= endPage; p++) {
      var pBtn = this.createPageBtn(p);
      this.navBox.appendChild(pBtn);
    }

    if (endPage < totalPages) {
      if (endPage < totalPages - 1) {
        var elipsis2 = document.createElement('span');
        elipsis2.className = 'paginator-ellipsis';
        elipsis2.textContent = '…';
        this.navBox.appendChild(elipsis2);
      }
      var pLast = this.createPageBtn(totalPages);
      this.navBox.appendChild(pLast);
    }

    // Botón Siguiente (›)
    var btnNext = document.createElement('button');
    btnNext.type = 'button';
    btnNext.className = 'paginator-btn' + (this.currentPage === totalPages ? ' disabled' : '');
    btnNext.disabled = this.currentPage === totalPages;
    btnNext.title = 'Página siguiente';
    btnNext.setAttribute('aria-label', 'Página siguiente');
    btnNext.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
    btnNext.onclick = function() { self.goToPage(self.currentPage + 1); };
    this.navBox.appendChild(btnNext);

    // Botón Última Página (»)
    var btnLast = document.createElement('button');
    btnLast.type = 'button';
    btnLast.className = 'paginator-btn' + (this.currentPage === totalPages ? ' disabled' : '');
    btnLast.disabled = this.currentPage === totalPages;
    btnLast.title = 'Última página';
    btnLast.setAttribute('aria-label', 'Ir a última página');
    btnLast.innerHTML = '<i class="fa-solid fa-angles-right"></i>';
    btnLast.onclick = function() { self.goToPage(totalPages); };
    this.navBox.appendChild(btnLast);
  };

  TablePaginator.prototype.createPageBtn = function(pageNum) {
    var self = this;
    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'paginator-btn' + (pageNum === this.currentPage ? ' active' : '');
    btn.textContent = pageNum;
    btn.setAttribute('aria-label', 'Página ' + pageNum);
    if (pageNum === this.currentPage) {
      btn.setAttribute('aria-current', 'page');
    }
    btn.onclick = function() {
      self.goToPage(pageNum);
    };
    return btn;
  };

  /**
   * Helper global para inicializar paginación con una sola llamada
   */
  window.initTablePagination = function(options) {
    return new TablePaginator(options);
  };

  window.TablePaginator = TablePaginator;

})(window);
