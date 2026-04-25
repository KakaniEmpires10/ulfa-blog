<form @submit.prevent="$dispatch('apply-filter', tempFilters)">
    <div class="columns is-bottom is-variable is-2 is-multiline" style="margin-bottom: 0;">

        <div class="column is-4-desktop">
            <div class="field">
                <label class="label is-small">Cari Judul</label>
                <div class="control has-icons-left">
                    <input class="input is-small" type="text" placeholder="Judul postingan..." x-model="tempFilters.title">
                    <span class="icon is-small is-left"><i class="fas fa-search"></i></span>
                </div>
            </div>
        </div>

        <div class="column is-2-desktop">
            <div class="field">
                <label class="label is-small">Status</label>
                <div class="control">
                    <div class="select is-small is-fullwidth">
                        <select x-model="tempFilters.status">
                            <option value="all">Semua</option>
                            <option value="draft">Draft</option>
                            <option value="published">Publish</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="column is-4-desktop">
            <div class="field">
                <label class="label is-small">Rentang Waktu</label>
                <div class="control has-icons-left"
                    x-init="calenderInstance = flatpickr($refs.rangePicker, { 
                            mode: 'range',
                            locale: 'id',
                            dateFormat: 'Y-m-d',
                            altInput: true,
                            altFormat: 'd F Y',
                            onClose: (selectedDates, dateStr, instance) => {
                                if (selectedDates.length === 2) {
                                    tempFilters.startDate = instance.formatDate(selectedDates[0], 'Y-m-d');
                                    tempFilters.endDate = instance.formatDate(selectedDates[1], 'Y-m-d');
                                }
                            }
                         })">
                    <input class="input is-small" type="text" x-ref="rangePicker" placeholder="Pilih rentang tanggal...">
                    <span class="icon is-small is-left">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="column is-2-desktop">
            <div class="field">
                <label class="label is-small is-hidden-mobile">&nbsp;</label>
                <div class="buttons">
                    <button type="submit" class="button is-link is-small">
                        <span class="icon"><i class="fas fa-filter"></i></span>
                        <span>Terapkan</span>
                    </button>
                    <button type="button" class="button is-small"
                        @click="
                            tempFilters.title = '';
                            tempFilters.status = 'all';
                            tempFilters.startDate = '';
                            tempFilters.endDate = '';
                            if (calenderInstance) calenderInstance.clear();
                            $dispatch('apply-filter', tempFilters);
                        "
                        @mouseenter="$store.tooltip.show($el, 'Bersihkan Filter', 'top')"
                        @mouseleave="$store.tooltip.hide()">
                        <span class="icon"><i class="fas fa-filter-circle-xmark"></i></span>
                    </button>
                </div>
            </div>
        </div>

    </div>
</form>