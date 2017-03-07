export default class TranslationLoader {
    constructor(TranslationService) {
        return () => {
            return TranslationService.getTranslations()
        }
    }
}