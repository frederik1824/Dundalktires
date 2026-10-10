import { createI18n } from 'vue-i18n'
import en from './en.js'
import es from './es.js'

function getStoredLocale() {
  try { return localStorage.getItem('dundalk_locale') || 'en' } catch { return 'en' }
}

export function storeLocale(locale) {
  try { localStorage.setItem('dundalk_locale', locale) } catch {}
}

export const i18n = createI18n({
  legacy: false,          // use Composition API
  locale: getStoredLocale(),
  fallbackLocale: 'en',
  messages: { en, es },
})

export default i18n
