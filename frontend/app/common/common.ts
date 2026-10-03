export type ID = string | number

export interface OptionItem<V extends ID = string> {
  label: string
  value: V
}
