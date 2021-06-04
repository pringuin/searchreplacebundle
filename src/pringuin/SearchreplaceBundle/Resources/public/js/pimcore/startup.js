pimcore.registerNS("pimcore.plugin.pringuinSearchreplaceBundle");

pimcore.plugin.pringuinSearchreplaceBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.pringuinSearchreplaceBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);

        this.navEl = Ext.get('pimcore_menu_search').insertSibling('<li id="pimcore_menu_searchreplace" data-menu-tooltip="Search And Replace" class="pimcore_menu_item pimcore_menu_needs_children"><img src="/bundles/pimcoreadmin/img/flat-white-icons/data_recovery.svg"></li>', 'after');
        this.menu = new Ext.menu.Menu({
            items: [{
                text: t("Searchreplace"),
                iconCls: "pimcore_icon_data_group_text",
                handler: this.openIndexPage
            }],
            cls: "pimcore_navigation_flyout"
        });
        pimcore.layout.toolbar.prototype.searchreplaceMenu = this.menu;
    },

    openIndexPage: function () {
            try {
                pimcore.globalmanager.get('searchreplaceadmin_index').activate();
            } catch (e) {
                pimcore.globalmanager.add('searchreplaceadmin_index', new pimcore.tool.genericiframewindow('index', '/pringuin_searchreplace', "pimcore_icon_data_group_text", 'Search And Replace'));
            }
    },

    pimcoreReady: function (params, broker) {
        // alert("pringuinSearchreplaceBundle ready!");
        var toolbar = pimcore.globalmanager.get("layout_toolbar");
        this.navEl.on("mousedown", toolbar.showSubMenu.bind(toolbar.searchreplaceMenu));
        pimcore.plugin.broker.fireEvent("searchreplaceMenuReady", toolbar.searchreplaceMenu);
    }
});

var pringuinSearchreplaceBundlePlugin = new pimcore.plugin.pringuinSearchreplaceBundle();
