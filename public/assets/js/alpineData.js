document.addEventListener('alpine:init', () => {
  const SIDEBAR_COOKIE_NAME = 'sidebar_state';
  const SIDEBAR_COOKIE_MAX_AGE = 60 * 60 * 24 * 7;

  const readCookie = (name) => {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);

    if (parts.length === 2) {
      return parts.pop().split(';').shift();
    }

    return null;
  };

  const writeCookie = (name, value, maxAge) => {
    document.cookie = `${name}=${value}; path=/; max-age=${maxAge}`;
  };

  Alpine.data('siteNav', () => ({
    open: false,
    scrolled: false,
    init() {
      const updateScrollState = () => {
        this.scrolled = window.scrollY > 24;
      };

      updateScrollState();
      window.addEventListener('scroll', updateScrollState, { passive: true });
    },
  }));

  Alpine.data('adminShell', () => ({
    sidebarOpen: false,
    sidebarCollapsed: false,
    isMobile: false,
    handleShortcut: null,
    init() {
      const syncSidebar = () => {
        this.isMobile = window.innerWidth < 1024;

        if (this.isMobile) {
          this.sidebarOpen = false;
          return;
        }

        this.sidebarOpen = true;
        this.sidebarCollapsed = readCookie(SIDEBAR_COOKIE_NAME) === 'false';
      };

      syncSidebar();
      window.addEventListener('resize', syncSidebar, { passive: true });

      this.handleShortcut = (event) => {
        if (event.key.toLowerCase() === '.' && (event.metaKey || event.ctrlKey)) {
          event.preventDefault();
          this.toggleSidebar();
        }
      };

      window.addEventListener('keydown', this.handleShortcut);
    },
    toggleSidebar() {
      if (this.isMobile) {
        this.sidebarOpen = !this.sidebarOpen;
        return;
      }

      this.sidebarCollapsed = !this.sidebarCollapsed;
      writeCookie(SIDEBAR_COOKIE_NAME, this.sidebarCollapsed ? 'false' : 'true', SIDEBAR_COOKIE_MAX_AGE);
    },
    closeSidebar() {
      if (this.isMobile) {
        this.sidebarOpen = false;
      }
    },
    state() {
      return this.sidebarCollapsed ? 'collapsed' : 'expanded';
    },
    destroy() {
      if (this.handleShortcut) {
        window.removeEventListener('keydown', this.handleShortcut);
      }
    },
  }));

  Alpine.data('adminDashboard', (config) => ({
    loading: true,
    error: '',
    stats: [],
    recentPosts: [],
    adminPages: [],
    async init() {
      await this.fetchData();
    },
    async fetchData() {
      this.loading = true;
      this.error = '';

      try {
        const response = await fetch(config.endpoint, {
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            Accept: 'application/json',
          },
        });

        if (!response.ok) {
          throw new Error('Gagal memuat data dashboard.');
        }

        const payload = await response.json();

        this.stats = Array.isArray(payload.stats) ? payload.stats : [];
        this.recentPosts = Array.isArray(payload.recentPosts) ? payload.recentPosts : [];
        this.adminPages = Array.isArray(payload.adminPages) ? payload.adminPages : [];
      } catch (error) {
        this.error = error.message || 'Terjadi kendala saat memuat dashboard.';
      } finally {
        this.loading = false;
      }
    },
  }));

  Alpine.data("fetchTags", config => ({
    loading: true,
    error: "",
    tags: [],
    filters: {
      name: new URLSearchParams(window.location.search).get("name") || "",
    },
    async init() {
      this.$watch("filters.name", value => {
        this.updateUrl();
        this.fetchData();
      });

      await this.fetchData();
    },
    async fetchData() {
      this.loading = true;
      this.error = "";

      try {
        const url = new URL(config.endpoint);
        if (this.filters.name) {
          url.searchParams.append("name", this.filters.name);
        }

        const response = await fetch(url, {
          headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
          },
        });

        if (!response.ok) {
          throw new Error("Gagal memuat data tags.");
        }

        const payload = await response.json();

        this.tags = Array.isArray(payload.data) ? payload.data : [];
      } catch (error) {
        this.error = error.message || "Terjadi kendala saat memuat tags.";
      } finally {
        this.loading = false;
      }
    },
    updateUrl() {
      const url = new URL(window.location.href);
      if (this.filters.name) {
        url.searchParams.set("name", this.filters.name);
      } else {
        url.searchParams.delete("name");
      }

      // Gunakan replaceState agar tidak mengotori history (back button tetap logis)
      window.history.replaceState({}, "", url);
    },
  }));

  Alpine.data("fetchCategories", config => ({
    loading: true,
    error: "",
    categories: [],
    filters: {
      name: new URLSearchParams(window.location.search).get("name") || "",
    },
    async init() {
      this.$watch("filters.name", value => {
        this.updateUrl();
        this.fetchData();
      });

      await this.fetchData();
    },
    async fetchData() {
      this.loading = true;
      this.error = "";

      try {
        const url = new URL(config.endpoint);
        if (this.filters.name) {
          url.searchParams.append("name", this.filters.name);
        }

        const response = await fetch(url, {
          headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
          },
        });

        if (!response.ok) {
          throw new Error("Gagal memuat data kategori.");
        }

        const payload = await response.json();

        this.categories = Array.isArray(payload.data) ? payload.data : [];
      } catch (error) {
        this.error = error.message || "Terjadi kendala saat memuat kategori.";
      } finally {
        this.loading = false;
      }
    },
    updateUrl() {
      const url = new URL(window.location.href);
      if (this.filters.name) {
        url.searchParams.set("name", this.filters.name);
      } else {
        url.searchParams.delete("name");
      }

      window.history.replaceState({}, "", url);
    },
  }));

  Alpine.data("fetchPosts", config => ({
    loading: true,
    error: "",
    posts: [],
    currentPage:
      parseInt(new URLSearchParams(window.location.search).get("page")) || 1,
    totalPages: 1,
    perPage: 15,
    updatingStatus: null,
    calenderInstance: null,
    filters: {
      title: new URLSearchParams(window.location.search).get("title") || "",
      status:
        new URLSearchParams(window.location.search).get("status") || "all",
      startDate:
        new URLSearchParams(window.location.search).get("startDate") || "",
      endDate: new URLSearchParams(window.location.search).get("endDate") || "",
    },

    async init() {
      await this.fetchData();
    },

    async fetchData() {
      this.loading = true;
      this.error = "";

      try {
        const url = new URL(config.endpoint, window.location.origin);

        // Hanya tambahkan ke query string jika ada nilainya
        Object.entries(this.filters).forEach(([key, value]) => {
          if (value && value !== "all") {
            url.searchParams.append(key, value);
          }
        });
        url.searchParams.append("page", this.currentPage);

        const response = await fetch(url, {
          headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
          },
        });

        if (!response.ok) throw new Error("Gagal memuat data posts.");

        const payload = await response.json();
        this.posts = Array.isArray(payload.data) ? payload.data : [];
        this.totalPages = payload.total_pages;
        this.currentPage = payload.current_page;

        this.updateUrl();
      } catch (error) {
        this.error = error.message;
      } finally {
        this.loading = false;
      }
    },

    setPage(p) {
      if (p > 0 && p <= this.totalPages) {
        this.currentPage = p;
        this.fetchData();
        window.scrollTo({ top: 0, behavior: "smooth" });
      }
    },

    getPaginationRange() {
        const total = this.totalPages;
        const current = this.currentPage;
        const delta = 2; // Jumlah halaman yang tampil di kiri/kanan halaman aktif
        const range = [];
        const rangeWithDots = [];
        let l;

        // Jika total halaman sedikit (misal < 8), tampilkan semua
        if (total <= 7) {
            for (let i = 1; i <= total; i++) range.push(i);
            return range;
        }

        // Logika perhitungan range dengan ellipses
        range.push(1);
        for (let i = current - delta; i <= current + delta; i++) {
            if (i < total && i > 1) {
                range.push(i);
            }
        }
        range.push(total);

        for (let i of range) {
            if (l) {
                if (i - l === 2) {
                    rangeWithDots.push(l + 1);
                } else if (i - l !== 1) {
                    rangeWithDots.push('...');
                }
            }
            rangeWithDots.push(i);
            l = i;
        }

        return rangeWithDots;
    },

    updateUrl() {
      const url = new URL(window.location.href);
      Object.entries(this.filters).forEach(([key, value]) => {
        if (value && value !== "all") {
          url.searchParams.set(key, value);
        } else {
          url.searchParams.delete(key);
        }
      });
      window.history.replaceState({}, "", url);
    },

    async updateStatus(id, newStatus, url) {
      if (!url) throw new Error("URL untuk memperbarui status tidak tersedia.");

      this.updatingStatus = id;

      try {
        const response = await fetch(url, {
          method: "PATCH",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ status: newStatus }),
        });

        if (!response.ok) throw new Error("Gagal memperbarui status post.");

        ButterPop.show({
          message: "Status berhasil diperbarui.",
          type: "success",
          position: "top-right",
          theme: "gradient",
          duration: 3000,
          progress: true,
          closable: true,
          pauseOnHover: true,
          closeOnClick: false,
        });

        await this.fetchData();
      } catch (err) {
        ButterPop.show({
          message: err.message || "Terjadi kesalahan saat memperbarui status.",
          type: "error",
          position: "top-right",
          theme: "gradient",
          duration: 3000,
          progress: true,
          closable: true,
          pauseOnHover: true,
          closeOnClick: false,
        });
      } finally {
        this.updatingStatus = null;
      }
    },
  }));

  // Image upload handler for setting
  Alpine.data("imageUploader", (config = {}) => ({
    previewUrl: config.previewUrl || "",
    originalUrl: config.originalUrl || "",
    isExternal: config.isExternal || false,
    isLoading: false,
    uploadProgress: 0,
    hasChanges: false,
    fileName: "",

    init() {
      this.$watch("hasChanges", value => {
        if (this.$refs.submitButton) {
          this.$refs.submitButton.disabled = !value;
        }
      });
    },

    updatePreview(event) {
      const file = event.target.files[0];
      if (!file) return;

      this.fileName = file.name;

      if (file.size > 4 * 1024 * 1024) {
        this.showNotification("warning", "Ukuran file maksimal 3 MB");
        event.target.value = "";
        return;
      }

      const allowedTypes = ["image/jpeg", "image/jpg", "image/png"];
      if (!allowedTypes.includes(file.type)) {
        this.showNotification(
          "warning",
          "Format file harus JPG, JPEG, atau PNG",
        );
        event.target.value = "";
        return;
      }

      if (config.type === "profile") {
        const img = new Image();
        const reader = new FileReader();

        reader.onload = e => {
          img.src = e.target.result;
          img.onload = () => {
            if (img.width !== img.height) {
              if (
                !confirm(
                  `⚠️ Gambar tidak persegi (1:1). Dimensi saat ini: ${img.width}x${img.height}\nTetap menggunakan gambar ini?`,
                )
              ) {
                event.target.value = "";
                return;
              }
            }
            this.previewUrl = URL.createObjectURL(file);
            this.isExternal = false;
            this.hasChanges = true;
            this.uploadProgress = 0;
          };
        };
        reader.readAsDataURL(file);
      } else {
        this.previewUrl = URL.createObjectURL(file);
        this.isExternal = false;
        this.hasChanges = true;
        this.uploadProgress = 0;
      }
    },

    resetImage() {
      if (this.isExternal) {
        this.previewUrl = this.originalUrl;
      } else {
        this.previewUrl = "";
        this.originalUrl = "";
      }
      this.hasChanges = false;
      this.uploadProgress = 0;
      this.fileName = "";

      // Reset file input
      const fileInput = document.getElementById(config.inputId);
      if (fileInput) fileInput.value = "";
    },

    async submitForm(event) {
      event.preventDefault();

      if (!this.hasChanges) return;

      this.isLoading = true;
      this.uploadProgress = 0;

      const fileInput = document.getElementById(config.inputId);

      if (!fileInput?.files?.[0]) {
        this.showNotification("warning", "Pilih file gambar terlebih dahulu");
        this.isLoading = false;
        return;
      }

      const formData = new FormData(event.target);
      const xhr = new XMLHttpRequest();

      // ─── Progress ───────────────────────────────────────────────
      xhr.upload.addEventListener("progress", e => {
        if (e.lengthComputable) {
          this.uploadProgress = Math.round((e.loaded / e.total) * 100);
        }
      });

      // ─── Response ───────────────────────────────────────────────
      xhr.onload = () => {
        try {
          const res = JSON.parse(xhr.responseText);

          if (res.status === "success") {
            // Tampilkan notifikasi dulu, baru redirect
            this.showNotification("success", res.message);
            setTimeout(() => {
              window.location.href = res.redirect;
            }, 1200);
          } else {
            this.isLoading = false;
            this.uploadProgress = 0;
            this.showNotification(
              "error",
              res.message ?? "Terjadi kesalahan saat upload",
            );
          }
        } catch {
          // Bukan JSON — redirect biasa (fallback)
          window.location.reload();
        }
      };

      xhr.onerror = () => {
        this.isLoading = false;
        this.uploadProgress = 0;
        this.showNotification("error", "Terjadi kesalahan jaringan");
      };

      // Tandai sebagai XHR agar controller mengenalinya
      xhr.open("POST", event.target.action);
      xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
      xhr.send(formData);
    },

    showNotification(type, message) {
      if (typeof ButterPop !== "undefined") {
        ButterPop.show({
          message,
          type:
            type === "success"
              ? "success"
              : type === "error"
                ? "error"
                : "warning",
          position: "top-right",
          theme: "gradient",
          duration: 5000,
          progress: true,
          closable: true,
          pauseOnHover: true,
          closeOnClick: false,
        });
      } else {
        alert(message);
      }
    }
  }));

  Alpine.data('heroSlider', (total) => ({
    active: 0,
    total,
    interval: null,
    get trackStyle() {
      return `transform: translateX(-${this.active * 100}%);`;
    },
    start() {
      if (this.total <= 1) {
        return;
      }

      this.pause();
      this.interval = setInterval(() => this.next(), 5000);
    },
    pause() {
      if (this.interval) {
        clearInterval(this.interval);
        this.interval = null;
      }
    },
    next() {
      this.active = (this.active + 1) % this.total;
    },
    prev() {
      this.active = (this.active - 1 + this.total) % this.total;
    },
    goTo(index) {
      this.active = index;
    },
  }));

  Alpine.store("tooltip").init();
});
