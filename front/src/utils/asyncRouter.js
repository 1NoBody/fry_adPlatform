// asyncRouter.js
//判断当前角色是否有访问权限
import Layout from '@/layout'
function hasPermission(roles, route) {
    return true;
  }
  // 递归过滤异步路由表，筛选角色权限路由
  export function getAsyncRoutes(routes) {
    const res = []
    // 定义路由中需要的自定名
    const keys = ['path', 'name', 'children', 'redirect', 'meta', 'hidden']
    // 遍历路由数组去重组可用的路由
    routes.forEach(item => {
        const newItem = {};
        if (item.component) {
            // 判断 item.component 是否等于 'Layout',若是则直接替换成引入的 Layout 组件
            if (item.component === 'Layout') {
                newItem.component = Layout
            } else {
            //  item.component 不等于 'Layout',则说明它是组件路径地址，因此直接替换成路由引入的方法
                newItem.component = resolve => require([`@/views/${item.component}`],resolve)
                
                // 此处用reqiure比较好，import引入变量会有各种莫名的错误
                // newItem.component = (() => import(`@/views/${item.component}`));
            }
        }
        for (const key in item) {
            if (keys.includes(key)) {
                newItem[key] = item[key]
            }
        }
        // 若遍历的当前路由存在子路由，需要对子路由进行递归遍历
        if (newItem.children && newItem.children.length) {
            newItem.children = getAsyncRoutes(item.children)
        }
        res.push(newItem)
    })
    // 返回处理好且可用的路由数组
    return res
}
 var menuDatas=[
  {
        "path": "/other",
        "component": "Layout",
        "redirect": "noRedirect",
        "name": "otherPage",
        "meta": {
            "title": "测试",
        },
        "children": [
            {
                "path": "a",
                "component": "order/index",
                "name": "a",
                "meta": { "title": "a页面", "noCache": "true" }
            },
            {
                "path": "b",
                "component": "order/index",
                "name": "b",
                "meta": { "title": "b页面", "noCache": "true" }
            },
        ]
    }
]
  export {menuDatas} 