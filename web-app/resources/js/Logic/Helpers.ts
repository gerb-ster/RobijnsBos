import dayjs from "dayjs";

/**
 * renderDate
 * @param timestamp
 */
export function renderDate(timestamp: string) {
    return dayjs(timestamp).format('DD-MM-YYYY');
}

/**
 * renderDate
 * @param timestamp
 */
export function renderUnixDate(timestamp: string) {
    return dayjs.unix(Number(timestamp)).format('DD-MM-YYYY');
}

/**
 * renderDateTime
 * @param timestamp
 */
export function renderDateTime(timestamp: string) {
    return dayjs(timestamp).format('DD-MM-YYYY hh:mm');
}

/**
 * storeInput
 * @param target
 * @param input
 * @param value
 */
export function storeInput(target:string, input: string, value: any) {
  let storedForm = openStorage(target); // extract stored form
  if (!storedForm) storedForm = {} // if none exists, default to empty object

  storedForm[input] = value // store new value
  localStorage.setItem(target, JSON.stringify(storedForm)) // save changes into localStorage
}

/**
 * openStorage
 * @param target
 */
export function openStorage(target: string) {
  return JSON.parse(localStorage.getItem(target));
}

/**
 *
 * @param a
 * @param obj
 */
export function contains(a: any, obj: any) {
  let i = a.length;

  while (i--) {
    if (a[i].id === obj.id) {
      return true;
    }
  }

  return false;
}

/**
 *
 * @param url
 */
export function openLink(url:string) {
  window.open(
    url,
    '_blank'
  );
}
