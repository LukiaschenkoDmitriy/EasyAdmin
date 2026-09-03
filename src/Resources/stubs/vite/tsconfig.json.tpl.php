{
  "compilerOptions": {
    "target": "es2023",
    "module": "esnext",
    "lib": ["ES2023", "DOM"],
    "skipLibCheck": true,
    "moduleResolution": "bundler",
    "moduleDetection": "force",
    "noEmit": true,
    "noUnusedLocals": true,
    "noUnusedParameters": true,
    "erasableSyntaxOnly": true,
    "noFallthroughCasesInSwitch": true,
    "ignoreDeprecations": "6.0",
    "experimentalDecorators": true,
    "useDefineForClassFields": false,
    "baseUrl": ".",
    "paths": {
      "@EAdmin/*": ["src/EAdmin/*"],
      "@EAdminCore/*": ["vendor/easy-admin/core/src/TS/*"]
    }
  },
  "include": ["src/EAdmin"]
}