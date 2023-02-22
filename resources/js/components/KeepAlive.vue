<script>
import {
  callWithAsyncErrorHandling,
  cloneVNode,
  getCurrentInstance,
  isVNode,
  onBeforeUnmount,
  onMounted,
  onUpdated,
  queuePostFlushCb,
  setTransitionHooks,
  warn,
  watch
} from 'vue'
import { invokeArrayFns, isArray, isFunction, isString } from '@vue/shared'

let devtools
let buffer = []
let devtoolsNotInstalled = false

const queuePostRenderEffect = queueEffectWithSuspense
const devtoolsComponentAdded = /*#__PURE__*/ createDevtoolsComponentHook('component:added' /* DevtoolsHooks.COMPONENT_ADDED */)
const isAsyncWrapper = (i) => !!i.type.__asyncLoader
const isSuspense = (type) => type.__isSuspense
const isProdDevTools = typeof global !== 'undefined' && global.__VUE_PROD_DEVTOOLS__ || null

function createDevtoolsComponentHook (hook) {
  return (component) => {
    emit(hook, component.appContext.app, component.uid, component.parent ? component.parent.uid : undefined, component)
  }
}

function emit (event, ...args) {
  if (devtools) {
    devtools.emit(event, ...args)
  } else if (!devtoolsNotInstalled) {
    buffer.push({ event, args })
  }
}

function getComponentName (Component, includeInferred = true) {
  return isFunction(Component)
      ? Component.displayName || Component.name
      : Component.name || (includeInferred && Component.__name)
}

function queueEffectWithSuspense (fn, suspense) {
  if (suspense && suspense.pendingBranch) {
    if (isArray(fn)) {
      suspense.effects.push(...fn)
    } else {
      suspense.effects.push(fn)
    }
  } else {
    queuePostFlushCb(fn)
  }
}

function matches (pattern, name) {
  if (isArray(pattern)) {
    return pattern.some((p) => matches(p, name))
  } else if (isString(pattern)) {
    return pattern.split(',').includes(name)
  } else if (pattern.test) {
    return pattern.test(name)
  }
  /* istanbul ignore next */
  return false
}

function resetShapeFlag (vnode) {
  let shapeFlag = vnode.shapeFlag
  if (shapeFlag & 256 /* ShapeFlags.COMPONENT_SHOULD_KEEP_ALIVE */) {
    shapeFlag -= 256 /* ShapeFlags.COMPONENT_SHOULD_KEEP_ALIVE */
  }
  if (shapeFlag & 512 /* ShapeFlags.COMPONENT_KEPT_ALIVE */) {
    shapeFlag -= 512 /* ShapeFlags.COMPONENT_KEPT_ALIVE */
  }
  vnode.shapeFlag = shapeFlag
}

function getInnerChild (vnode) {
  return vnode.shapeFlag & 128 /* ShapeFlags.SUSPENSE */ ? vnode.ssContent : vnode
}

function invokeVNodeHook (hook, instance, vnode, prevVNode = null) {
  callWithAsyncErrorHandling(hook, instance, 7 /* ErrorCodes.VNODE_HOOK */, [
    vnode,
    prevVNode
  ])
}

export default {
  name: 'KeepAliveComponent',
  // Marker for special handling inside the renderer. We are not using a ===
  // check directly on KeepAlive in the renderer, because importing it directly
  // would prevent it from being tree-shaken.
  __isKeepAlive: true,
  props: {
    include: [String, RegExp, Array],
    exclude: [String, RegExp, Array],
    max: [String, Number]
  },
  setup (props, { slots }) {
    const instance = getCurrentInstance()
    // KeepAlive communicates with the instantiated renderer via the
    // ctx where the renderer passes in its internals,
    // and the KeepAlive instance exposes activate/deactivate implementations.
    // The whole point of this is to avoid importing KeepAlive directly in the
    // renderer to facilitate tree-shaking.
    const sharedContext = instance.ctx
    // if the internal renderer is not registered, it indicates that this is server-side rendering,
    // for KeepAlive, we just need to render its children
    if (!sharedContext.renderer) {
      return () => {
        const children = slots.default && slots.default()
        return children && children.length === 1 ? children[0] : children
      }
    }
    const cache = new Map()
    const keys = new Set()
    let current = null
    if ((process.env.NODE_ENV !== 'production') || isProdDevTools) {
      instance.__v_cache = cache
    }
    const parentSuspense = instance.suspense
    const { renderer: { p: patch, m: move, um: _unmount, o: { createElement } } } = sharedContext
    const storageContainer = createElement('div')
    sharedContext.activate = (vnode, container, anchor, isSVG, optimized) => {
      const instance = vnode.component
      move(vnode, container, anchor, 0 /* MoveType.ENTER */, parentSuspense)
      // in case props have changed
      patch(instance.vnode, vnode, container, anchor, instance, parentSuspense, isSVG, vnode.slotScopeIds, optimized)
      queuePostRenderEffect(() => {
        instance.isDeactivated = false
        if (instance.a) {
          invokeArrayFns(instance.a)
        }
        const vnodeHook = vnode.props && vnode.props.onVnodeMounted
        if (vnodeHook) {
          invokeVNodeHook(vnodeHook, instance.parent, vnode)
        }
      }, parentSuspense)
      if ((process.env.NODE_ENV !== 'production') || isProdDevTools) {
        // Update components tree
        devtoolsComponentAdded(instance)
      }
    }
    sharedContext.deactivate = (vnode) => {
      const instance = vnode.component
      move(vnode, storageContainer, null, 1 /* MoveType.LEAVE */, parentSuspense)
      queuePostRenderEffect(() => {
        if (instance.da) {
          invokeArrayFns(instance.da)
        }
        const vnodeHook = vnode.props && vnode.props.onVnodeUnmounted
        if (vnodeHook) {
          invokeVNodeHook(vnodeHook, instance.parent, vnode)
        }
        instance.isDeactivated = true
      }, parentSuspense)
      if ((process.env.NODE_ENV !== 'production') || isProdDevTools) {
        // Update components tree
        devtoolsComponentAdded(instance)
      }
    }

    function unmount (vnode) {
      // reset the shapeFlag so it can be properly unmounted
      resetShapeFlag(vnode)
      _unmount(vnode, instance, parentSuspense, true)
    }

    function pruneCache (filter) {
      cache.forEach((vnode, key) => {
        const name = vnode.key || getComponentName(vnode.type)
        if (name && (!filter || !filter(name))) {
          pruneCacheEntry(key)
        }
      })
    }

    function pruneCacheEntry (key) {
      const cached = cache.get(key)
      if (!current || cached.key !== current.key || cached.type !== current.type) {
        unmount(cached)
      } else if (current) {
        // current active instance should no longer be kept-alive.
        // we can't unmount it now but it might be later, so reset its flag now.
        resetShapeFlag(current)
      }
      cache.delete(key)
      keys.delete(key)
    }

    // prune cache on include/exclude prop change
    watch(() => [props.include, props.exclude], ([include, exclude]) => {
          include && pruneCache(name => matches(include, name))
          exclude && pruneCache(name => !matches(exclude, name))
        },
        // prune post-render after `current` has been updated
        { flush: 'post', deep: true })
    // cache sub tree after render
    let pendingCacheKey = null
    const cacheSubtree = () => {
      // fix #1621, the pendingCacheKey could be 0
      if (pendingCacheKey != null) {
        cache.set(pendingCacheKey, getInnerChild(instance.subTree))
      }
    }
    onMounted(cacheSubtree)
    onUpdated(cacheSubtree)
    onBeforeUnmount(() => {
      cache.forEach(cached => {
        const { subTree, suspense } = instance
        const vnode = getInnerChild(subTree)
        if (cached.type === vnode.type) {
          // current instance will be unmounted as part of keep-alive's unmount
          resetShapeFlag(vnode)
          // but invoke its deactivated hook here
          const da = vnode.component.da
          da && queuePostRenderEffect(da, suspense)
          return
        }
        unmount(cached)
      })
    })
    return () => {
      pendingCacheKey = null
      if (!slots.default) {
        return null
      }
      const children = slots.default()
      const rawVNode = children[0]
      if (children.length > 1) {
        if ((process.env.NODE_ENV !== 'production')) {
          warn(`KeepAlive should contain exactly one component child.`)
        }
        current = null
        return children
      } else if (!isVNode(rawVNode) ||
          (!(rawVNode.shapeFlag & 4 /* ShapeFlags.STATEFUL_COMPONENT */) &&
              !(rawVNode.shapeFlag & 128 /* ShapeFlags.SUSPENSE */))) {
        current = null
        return rawVNode
      }
      let vnode = getInnerChild(rawVNode)
      const comp = vnode.type
      // for async components, name check should be based in its loaded
      // inner component if available
      const name = vnode.key || getComponentName(isAsyncWrapper(vnode)
          ? vnode.type.__asyncResolved || {}
          : comp)
      const { include, exclude, max } = props
      if ((include && (!name || !matches(include, name))) ||
          (exclude && name && matches(exclude, name))) {
        current = vnode
        return rawVNode
      }
      const key = vnode.key == null ? comp : vnode.key
      const cachedVNode = cache.get(key)
      // clone vnode if it's reused because we are going to mutate it
      if (vnode.el) {
        vnode = cloneVNode(vnode)
        if (rawVNode.shapeFlag & 128 /* ShapeFlags.SUSPENSE */) {
          rawVNode.ssContent = vnode
        }
      }
      // #1513 it's possible for the returned vnode to be cloned due to attr
      // fallthrough or scopeId, so the vnode here may not be the final vnode
      // that is mounted. Instead of caching it directly, we store the pending
      // key and cache `instance.subTree` (the normalized vnode) in
      // beforeMount/beforeUpdate hooks.
      pendingCacheKey = key
      if (cachedVNode) {
        // copy over mounted state
        vnode.el = cachedVNode.el
        vnode.component = cachedVNode.component
        if (vnode.transition) {
          // recursively update transition hooks on subTree
          setTransitionHooks(vnode, vnode.transition)
        }
        // avoid vnode being mounted as fresh
        vnode.shapeFlag |= 512 /* ShapeFlags.COMPONENT_KEPT_ALIVE */
        // make this key the freshest
        keys.delete(key)
        keys.add(key)
      } else {
        keys.add(key)
        // prune oldest entry
        if (max && keys.size > parseInt(max, 10)) {
          pruneCacheEntry(keys.values().next().value)
        }
      }
      // avoid vnode being unmounted
      vnode.shapeFlag |= 256 /* ShapeFlags.COMPONENT_SHOULD_KEEP_ALIVE */
      current = vnode
      return isSuspense(rawVNode.type) ? rawVNode : vnode
    }
  }
}
</script>
