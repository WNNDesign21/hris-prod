// vite.config.js
import { defineConfig } from "file:///C:/Users/User/Documents/PROJECT%20TCF%20202/tcf-superapps/node_modules/vite/dist/node/index.js";
import laravel from "file:///C:/Users/User/Documents/PROJECT%20TCF%20202/tcf-superapps/node_modules/laravel-vite-plugin/dist/index.js";
var vite_config_default = defineConfig({
  plugins: [
    laravel({
      input: [
        "resources/sass/app.scss",
        "resources/css/app.css",
        "resources/js/app.js",
        "resources/js/pages/master-data-dashboard.js",
        "resources/js/pages/turnover.js",
        "resources/js/pages/organisasi.js",
        "resources/js/pages/departemen.js",
        "resources/js/pages/divisi.js",
        "resources/js/pages/grup.js",
        "resources/js/pages/jabatan.js",
        "resources/js/pages/karyawan.js",
        "resources/js/pages/kontrak.js",
        "resources/js/pages/posisi.js",
        "resources/js/pages/seksi.js",
        "resources/js/pages/export.js",
        "resources/js/pages/template.js",
        "resources/js/pages/event.js",
        "resources/js/pages/cutie-dashboard.js",
        "resources/js/pages/cutie-member-cuti.js",
        "resources/js/pages/cutie-pengajuan-cuti.js",
        "resources/js/pages/cutie-personalia-cuti.js",
        "resources/js/pages/cutie-export.js",
        "resources/js/pages/cutie-setting.js",
        "resources/js/pages/cutie-bypass-cuti.js",
        "resources/js/pages/menu.js",
        "resources/js/pages/lembure-pengajuan-lembur.js",
        "resources/js/pages/lembure-approval-lembur.js",
        "resources/js/pages/lembure-detail-lembur.js",
        "resources/js/pages/lembure-setting-upah-lembur.js",
        "resources/js/pages/lembure-setting-lembur.js",
        "resources/js/pages/lembure-dashboard.js",
        "resources/js/pages/lembure-setting-gaji-departemen.js",
        "resources/js/pages/izine-pengajuan-izin.js",
        "resources/js/pages/izine-lapor-skd.js"
      ],
      // refresh: true,
      refresh: ["resources/views/**", "resources/css/**", "app/Http/**"]
    })
  ]
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJDOlxcXFxVc2Vyc1xcXFxVc2VyXFxcXERvY3VtZW50c1xcXFxQUk9KRUNUIFRDRiAyMDJcXFxcdGNmLXN1cGVyYXBwc1wiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9maWxlbmFtZSA9IFwiQzpcXFxcVXNlcnNcXFxcVXNlclxcXFxEb2N1bWVudHNcXFxcUFJPSkVDVCBUQ0YgMjAyXFxcXHRjZi1zdXBlcmFwcHNcXFxcdml0ZS5jb25maWcuanNcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfaW1wb3J0X21ldGFfdXJsID0gXCJmaWxlOi8vL0M6L1VzZXJzL1VzZXIvRG9jdW1lbnRzL1BST0pFQ1QlMjBUQ0YlMjAyMDIvdGNmLXN1cGVyYXBwcy92aXRlLmNvbmZpZy5qc1wiO2ltcG9ydCB7IGRlZmluZUNvbmZpZyB9IGZyb20gJ3ZpdGUnO1xuaW1wb3J0IGxhcmF2ZWwgZnJvbSAnbGFyYXZlbC12aXRlLXBsdWdpbic7XG5cbmV4cG9ydCBkZWZhdWx0IGRlZmluZUNvbmZpZyh7XG4gICAgcGx1Z2luczogW1xuICAgICAgICBsYXJhdmVsKHtcbiAgICAgICAgICAgIGlucHV0OiBbXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9zYXNzL2FwcC5zY3NzJyxcbiAgICAgICAgICAgICAgICBcInJlc291cmNlcy9jc3MvYXBwLmNzc1wiLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvanMvYXBwLmpzJyxcbiAgICAgICAgICAgICAgICAncmVzb3VyY2VzL2pzL3BhZ2VzL21hc3Rlci1kYXRhLWRhc2hib2FyZC5qcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9wYWdlcy90dXJub3Zlci5qcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9wYWdlcy9vcmdhbmlzYXNpLmpzJyxcbiAgICAgICAgICAgICAgICAncmVzb3VyY2VzL2pzL3BhZ2VzL2RlcGFydGVtZW4uanMnLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvanMvcGFnZXMvZGl2aXNpLmpzJyxcbiAgICAgICAgICAgICAgICAncmVzb3VyY2VzL2pzL3BhZ2VzL2dydXAuanMnLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvanMvcGFnZXMvamFiYXRhbi5qcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9wYWdlcy9rYXJ5YXdhbi5qcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9wYWdlcy9rb250cmFrLmpzJyxcbiAgICAgICAgICAgICAgICAncmVzb3VyY2VzL2pzL3BhZ2VzL3Bvc2lzaS5qcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9wYWdlcy9zZWtzaS5qcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9wYWdlcy9leHBvcnQuanMnLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvanMvcGFnZXMvdGVtcGxhdGUuanMnLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvanMvcGFnZXMvZXZlbnQuanMnLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvanMvcGFnZXMvY3V0aWUtZGFzaGJvYXJkLmpzJyxcbiAgICAgICAgICAgICAgICAncmVzb3VyY2VzL2pzL3BhZ2VzL2N1dGllLW1lbWJlci1jdXRpLmpzJyxcbiAgICAgICAgICAgICAgICAncmVzb3VyY2VzL2pzL3BhZ2VzL2N1dGllLXBlbmdhanVhbi1jdXRpLmpzJyxcbiAgICAgICAgICAgICAgICAncmVzb3VyY2VzL2pzL3BhZ2VzL2N1dGllLXBlcnNvbmFsaWEtY3V0aS5qcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9wYWdlcy9jdXRpZS1leHBvcnQuanMnLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvanMvcGFnZXMvY3V0aWUtc2V0dGluZy5qcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9wYWdlcy9jdXRpZS1ieXBhc3MtY3V0aS5qcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9wYWdlcy9tZW51LmpzJyxcbiAgICAgICAgICAgICAgICAncmVzb3VyY2VzL2pzL3BhZ2VzL2xlbWJ1cmUtcGVuZ2FqdWFuLWxlbWJ1ci5qcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9wYWdlcy9sZW1idXJlLWFwcHJvdmFsLWxlbWJ1ci5qcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9wYWdlcy9sZW1idXJlLWRldGFpbC1sZW1idXIuanMnLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvanMvcGFnZXMvbGVtYnVyZS1zZXR0aW5nLXVwYWgtbGVtYnVyLmpzJyxcbiAgICAgICAgICAgICAgICAncmVzb3VyY2VzL2pzL3BhZ2VzL2xlbWJ1cmUtc2V0dGluZy1sZW1idXIuanMnLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvanMvcGFnZXMvbGVtYnVyZS1kYXNoYm9hcmQuanMnLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvanMvcGFnZXMvbGVtYnVyZS1zZXR0aW5nLWdhamktZGVwYXJ0ZW1lbi5qcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9wYWdlcy9pemluZS1wZW5nYWp1YW4taXppbi5qcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9wYWdlcy9pemluZS1sYXBvci1za2QuanMnLFxuICAgICAgICAgICAgXSxcbiAgICAgICAgICAgIC8vIHJlZnJlc2g6IHRydWUsXG4gICAgICAgICAgICByZWZyZXNoOiBbXCJyZXNvdXJjZXMvdmlld3MvKipcIiwgXCJyZXNvdXJjZXMvY3NzLyoqXCIsIFwiYXBwL0h0dHAvKipcIl0sXG4gICAgICAgIH0pLFxuICAgIF0sXG59KTtcbiJdLAogICJtYXBwaW5ncyI6ICI7QUFBaVcsU0FBUyxvQkFBb0I7QUFDOVgsT0FBTyxhQUFhO0FBRXBCLElBQU8sc0JBQVEsYUFBYTtBQUFBLEVBQ3hCLFNBQVM7QUFBQSxJQUNMLFFBQVE7QUFBQSxNQUNKLE9BQU87QUFBQSxRQUNIO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsTUFDSjtBQUFBO0FBQUEsTUFFQSxTQUFTLENBQUMsc0JBQXNCLG9CQUFvQixhQUFhO0FBQUEsSUFDckUsQ0FBQztBQUFBLEVBQ0w7QUFDSixDQUFDOyIsCiAgIm5hbWVzIjogW10KfQo=
