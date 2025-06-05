import { type HTMLAttributes } from 'vue'

export const Table = (props: HTMLAttributes) => {
  return (
    <table
      {...props}
      class={[
        'w-full caption-bottom text-sm',
        props.class ?? '',
      ]}
    />
  )
} 