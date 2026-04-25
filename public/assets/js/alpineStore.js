document.addEventListener("alpine:init", () => {
  Alpine.store("theme", {
    mode: window.APP?.themeMode || "light",
  });

  Alpine.store("deleteModal", {
    isOpen: false,
    title: "Hapus Data",
    description: "Apakah Anda yakin ingin menghapus data ini?",
    deleteLabel: "Ya, Hapus",
    deleteUrl: "",
    isLoading: false,

    open(config = {}) {
      this.title = config.title || "Hapus Data";
      this.description =
        config.description || "Tindakan ini tidak dapat dibatalkan.";
      this.deleteLabel = config.deleteLabel || "Ya, Hapus";
      this.deleteUrl = config.url || "";
      this.isOpen = true;
    },

    close() {
      if (this.isLoading) return;
      this.isOpen = false;
      this.deleteUrl = "";
    },

    confirm() {
      if (!this.deleteUrl) return;
      this.isLoading = true;

      const form = document.getElementById("global-delete-form");
      if (form) {
        form.action = this.deleteUrl;
        form.submit();
      }
    },
  });

  Alpine.store("tooltip", {
    el: null,
    arrowEl: null,
    contentEl: null, // Tambahkan ini untuk menampung teks
    cleanup: null,

    init() {
      if (!this.el) {
        this.el = document.createElement("div");
        this.el.className = "floating-tooltip";

        // 1. Buat element teks (agar tidak ikut miring)
        this.contentEl = document.createElement("span");
        this.contentEl.className = "tooltip-content";

        // 2. Buat element arrow
        this.arrowEl = document.createElement("div");
        this.arrowEl.className = "tooltip-arrow";

        this.el.appendChild(this.contentEl);
        this.el.appendChild(this.arrowEl);
        document.body.appendChild(this.el);
      }
    },

    async show(target, text, side = "top", variant = "") {
      if (!text || !window.FloatingUIDOM) return;

      const { computePosition, offset, flip, shift, arrow, autoUpdate } =
        window.FloatingUIDOM;

      // Update class dan teks ke element yang benar
      this.el.className = `floating-tooltip ${variant}`;
      this.contentEl.textContent = text; // Set teks ke contentEl, bukan ke root el

      if (this.cleanup) this.cleanup();

      const updatePosition = async () => {
        const { x, y, placement, middlewareData } = await computePosition(
          target,
          this.el,
          {
            placement: side,
            strategy: "fixed",
            middleware: [
              offset(8),
              flip(),
              shift({ padding: 5 }),
              arrow({ element: this.arrowEl }),
            ],
          },
        );

        Object.assign(this.el.style, {
          left: `${x}px`,
          top: `${y}px`,
        });

        // Logika posisi arrow
        const { x: arrowX, y: arrowY } = middlewareData.arrow;
        const staticSide = {
          top: "bottom",
          right: "left",
          bottom: "top",
          left: "right",
        }[placement.split("-")[0]];

        Object.assign(this.arrowEl.style, {
          left: arrowX != null ? `${arrowX}px` : "",
          top: arrowY != null ? `${arrowY}px` : "",
          right: "",
          bottom: "",
          [staticSide]: "-4px",
        });
      };

      this.cleanup = autoUpdate(target, this.el, updatePosition);
      this.el.classList.add("is-active");
    },

    hide() {
      if (this.el) this.el.classList.remove("is-active");
      if (this.cleanup) {
        this.cleanup();
        this.cleanup = null;
      }
    },
  });

  Alpine.store("helpers", {
    getCoverUrl(coverPath) {
      if (!coverPath) {
        return "https://placehold.co/600x400?text=No+Cover";
      }

      // Cek apakah external URL
      if (coverPath.match(/^https?:\/\//)) {
        return coverPath;
      }

      // Internal path
      const baseUrl = window.baseUrl || "";
      const cleanPath = coverPath.replace(/^\//, "");
      return window.APP.baseUrl + "/" + cleanPath;
    },

    formatDate(dateString) {
      if (!dateString) return "-";
      return new Date(dateString).toLocaleDateString("id-ID");
    },

    truncateText(text, length = 100) {
      if (!text) return "";
      if (text.length <= length) return text;
      return text.substring(0, length) + "...";
    },
  });
});