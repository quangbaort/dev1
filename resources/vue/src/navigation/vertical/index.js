import appAndPages from './app-and-pages'
import dashboard from './dashboard'
import others from './others'
import { permissions } from '@/common'
const userMenu = JSON.parse(localStorage.getItem('userMenu')) ?? []
const getUserMenu = () => {
  if(userMenu.length > 0) {
    let menu = userMenu.map((item) => {
      return item.function
    })
    const menuDashBoard = dashboard.filter(item => {
      if (menu.includes(item.function)) {
        return item
      }
    })
    const appPage = appAndPages.filter(item => {
      if (menu.includes(item.function)) {
        return item
      }
    })
    const otherPage = others.filter(item => {
      if (menu.includes(item.function)) {
        return item
      }
    })
    return [...menuDashBoard, ...appPage, ...otherPage]
  } else {
    return []
  }
}
export default getUserMenu()

