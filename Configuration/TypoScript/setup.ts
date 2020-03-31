# Module configuration
module.tx_nsbackup_tools_nsbackupbackup {
    persistence {
        storagePid = {$module.tx_nsbackup_backup.persistence.storagePid}
    }
    view {
        templateRootPaths.0 = EXT:ns_backup/Resources/Private/Templates/
        templateRootPaths.1 = {$module.tx_nsbackup_backup.view.templateRootPath}
        partialRootPaths.0 = EXT:ns_backup/Resources/Private/Partials/
        partialRootPaths.1 = {$module.tx_nsbackup_backup.view.partialRootPath}
        layoutRootPaths.0 = EXT:ns_backup/Resources/Private/Layouts/
        layoutRootPaths.1 = {$module.tx_nsbackup_backup.view.layoutRootPath}
    }
}


ajaxData = PAGE
ajaxData {
    typeNum = 221843008
    config {
        disableAllHeaderCode = 1
        additionalHeaders = Content-type:application/html
        xhtml_cleaning = 0
        debug = 0
        no_cache = 1
        admPanel = 0
    }
}
